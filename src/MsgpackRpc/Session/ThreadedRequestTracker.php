<?php
declare(strict_types=1);

namespace Xerkus\Neovim\MsgpackRpc\Session;

use Threaded;

class ThreadedRequestTracker extends Threaded
{
    const HANDLER_TIMEOUT = 300;

    private $id = 1;
    private $responseHandlers = [];

    public function getNextRequestId()
    {
        return $this->synchronized(function() {
            $id = $this->id;
            // I know about ++, increment does not work on threaded!
            $this->id = $this->id + 1;
            return $id;
        });
    }

    public function pushResponseHandler($id, Callable $callable)
    {
        $this->synchronized(function($id, $callable) {
            $id = 'id' . $id;
            $this->responseHandlers[$id] = $callable;
        }, $id, $callable);
    }

    public function popResponseHandler($id)
    {
        return $this->synchronized(function($id) {
            $id = 'id' . $id;
            if (!isset($this->responseHandlers[$id])) {
                return;
            }
            $handler = $this->responseHandlers[$id];
            unset($this->responseHandlers[$id]);
            return $handler;
        }, $id);
    }
}
