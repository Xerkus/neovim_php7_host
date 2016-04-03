<?php
declare(strict_types=1);

namespace Xerkus\Neovim\Plugin;

use RuntimeException;
use Xerkus\Neovim\MsgpackRpc\Session;
use Xerkus\Neovim\MsgpackRpc\Response;

/**
 * Plugin host
 */
class Host
{
    private $session;
    private $plugins = [];
    private $requestHandlers = [];
    private $notificationHandlers = [];

    public function __construct(Session $session)
    {
        $this->session = $session;
        $this->requestHandlers = [
            'poll' => function ($args, Response $response) {
                $response->send('ok');
            },
            'specs' => [$this, 'onSpecsRequest'],
            'shutdown' => [$this, 'shutdown'],
        ];
    }

    public function start(array $plugins)
    {
        if (count($plugins) < 1) {
            throw new \RuntimException('Must specify at least one plugin');
        }
        $this->loadPlugins($plugins);
        $this->session->sessionStart(
            [$this, 'onRequest'],
            [$this, 'onNotification']
        );
    }

    public function loadPlugins(array $plugins)
    {
        foreach ($plugins as $pluginPath) {
            if (!is_readable($pluginPath)) {
                // send error plugin is not readable?
                continue;
            }
            if (array_key_exists($pluginPath, $this->plugins)) {
                // plugin is already loaded, skip
                continue;
            }
            /** @var $plugin StdClass */
            $plugin = new ThreadedContainer($pluginPath, $this->session);
            // start all of the containers first, handle failed later
            $plugin->start();
            $this->plugins[$pluginPath] = $plugin;
        }
        $this->initHandlers();
    }

    public function onRequest($rpcMethod, $args, Response $response)
    {
        if (isset($this->requestHandlers[$rpcMethod])) {
            $this->requestHandlers[$rpcMethod]($args, $response);
        }

        //@TODO handle missing handler
    }

    public function onNotification($rpcMethod, $args)
    {
        if (isset($this->notificationHandlers[$rpcMethod])) {
            $this->notificationHandlers[$rpcMethod]($args);
        }
        //@TODO handle missing handler
    }

    public function onSpecsRequest($path, Response $response)
    {
        if (!isset($this->plugins[$path])) {
            return $response->error('Plugin in path ' . $path . ' not found');
        }
        if (!$this->plugins[$path]->isRunning()) {
            return $response->error('Spec request failed. Plugin thread gone away');
        }
        $handlers = $this->plugins[$path]->getRegisteredRpcHandlers();
        $specs = [];
        foreach ($handlers as $handler) {
            $spec = $handler->getSpec();
            if (!$spec->getShouldExport()) {
                continue;
            }
            $specs[] = $spec->getSpecArray();
        }
        $response->send($specs);
    }

    public function shutdown()
    {
        //$this->unloadPlugins();
        $this->session->stop();
    }

    private function initHandlers()
    {

    }
}
