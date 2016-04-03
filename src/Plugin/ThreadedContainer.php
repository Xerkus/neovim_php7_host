<?php
declare(strict_types=1);

namespace Xerkus\Neovim\Plugin;

use RuntimeException;
use Thread;
use Xerkus\Neovim\Nvim;

class ThreadedContainer extends Thread
{
    /**
     * Timeout for plugins to provide handlers.
     *
     * 30 seconds is probably too high. Plugins should do lazy init
     */
    const PLUGIN_INIT_TIMEOUT = 30;

    private $pluginPath;
    private $nvim;
    private $commandQueue;
    private $registerAutoloader;
    private $registeredRpcHandlers = [];

    private static $plugin;
    private static $requestHandlers = [];
    private static $notificationHandlers = [];
    private static $responseHandlers = [];

    public function __construct(
        string $path,
        Nvim $nvim,
        callable $registerAutoloader = null
    ) {
        $this->pluginPath = $path;
        $this->nvim = $nvim;
        $this->commandQueue = new Threaded;
        $this->registerAutoloader = $registerAutoloader ?: function () {
        };
    }

    public function run()
    {
        ($this->registerAutoloader)();
        $plugin = $this->loadPlugin();
        $this->initHandlers($plugin);

        while (true) {
            $this->synchronized(function () {
                while (count($this->commandQueue) < 1) {
                    // wait for command
                    $this->wait();
                }
            });

            $command = $this->commandQueue->shift();
            $command();
        };
    }

    public function handleRequest($message, Response $response)
    {
        $this->synchronized(function ($message) {
            $this->queue[] = doSomething($message);
            $this->notify();
        }, $message);
    }

    public function handleNotification($message)
    {
        $this->synchronized(function () use ($message) {
            $this->queue[] = doSomething($message);
            $this->notify();
        });
    }

    public function handleResponse($messageId, $responseMessage)
    {
        $this->synchronized(function ($message) {
            $this->queue[] = doSomething($message);
            $this->notify();
        });
    }

    public function getRegisteredRpcHandlers() : array
    {
        return $this->synchronized(function () {
            $stime = time();
            // @TODO wait on handlers AND thread state?
            while ($this->registeredRpcHandlers == null) {
                if (time() - $stime >= self::PLUGIN_INIT_TIMEOUT) {
                    throw new RuntimeException(
                        'Plugin failed to provide rpc handlers in a timely fashion'
                    );
                }
                $this->wait(self::PLUGIN_INIT_TIMEOUT);
            }
            return $this->registeredRpcHandlers;
        });
    }

    private function loadPlugin()
    {
        if ('php' !== pathinfo($this->pluginPath, PATHINFO_EXTENSION)) {
            throw new RuntimeException('Plugin path must point to a php file');
        }
        if (!is_readable($this->pluginPath)) {
            throw new RuntimeException('Plugin path is not readable');
        }
        $nvim = $this->nvim;

        /**
         * @var $plugin Plugin
         */
        $plugin = include $this->pluginPath;
        if (!$plugin instanceof Plugin) {
            throw new RuntimeException(
                'No valid plugin instance was returned by ' . $this->pluginPath
            );
        }


        return $plugin;
    }

    private function initHandlers(Plugin $plugin)
    {
        $handlers = $plugin->getRpcHandlers();
        if (empty($handlers)) {
            throw new RuntimeException('Plugin does not provide any handlers');
        }
        $exposeHandlers = [];
        foreach ($handlers as $handler) {
            $spec = $handler->getSpec()->withPluginPath($this->pluginPath);
            $handler = new Handler($spec, $handler->getCallback());
            $methodName = $spec->getMethodName();
            if ($spec->getIsSync()) {
                // @TODO throw exception if such handler already exists
                self::$requestHandlers[$methodName] = $handler;
                $exposeHandlers[] = $handler->getSpec();
            } else {
                // @TODO throw exception if such handler already exists
                self::$notificationHandlers[$methodName] = $handler;
                $exposeHandlers[] = $handler->getSpec();
            }
        }
        $this->synchronized(function (array $registeredRpcHandlers) {
            $this->registeredRpcHandlers = $registeredRpcHandlers;
            $this->notify();
        }, $exposeHandlers);
    }
}
