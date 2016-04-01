<?php
declare(strict_types=1);

namespace Xerkus\Neovim\Plugin;

use RuntimeException;
use Xerkus\Neovim\MsgpackRpc\Session;

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
            'poll' => function($msg, Response $response) {
                $response->send('ok');
            }
        ];
    }

    public function start(array $plugins)
    {
        if (count($plugins) < 1) {
            throw new \RuntimException('Must specify at least one plugin');
        }
        $this->session->run(
            [$this, 'onRequest'],
            [$this, 'onNotification'],
            function() use ($plugins) {
                $this->loadPlugins($plugins);
            }
        );
    }

    public function loadPlugins(array $plugins)
    {
        foreach ($plugins as $pluginPath) {
            $pluginPath = realpath($pluginPath);
            if (!is_readable($pluginPath)) {
                // send error plugin is not readable?
                continue;
            }
            if (array_key_exists($pluginPath, $this->plugins)) {
                // plugin is already loaded, skip
                continue;
            }
            /** @var $plugin StdClass */
            $plugin = $this->startPluginContainer($pluginPath);

            $plugin->getHandlersDefinitions()
            $handlers = $this->loadPlugin($pluginPath);

            $this->plugins[$pluginPath] = $this->startPluginContainer($pluginPath);
        }
        $this->initHandlers();
    }

    public function onRequest($message, Response $response)
    {
        if (isset($this->requestHandlers[$message[2]])) {
            $this->requestHandlers[$message[2]]($message, $response);
        }

        //@TODO handle missing handler
    }

    public function onNotification($message)
    {

    }
}
