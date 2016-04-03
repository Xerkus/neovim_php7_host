<?php
declare(strict_types=1);

namespace Xerkus\Neovim\MsgpackRpc;

use Threaded;
use Xerkus\Neovim\MsgpackRpc\Session\ThreadedRequestTracker;

class Session extends Threaded
{
    const TYPE_REQUEST = 0;
    const TYPE_RESPONSE = 1;
    const TYPE_NOTIFICATION = 2;

    /**
     * @var Stream
     */
    private $stream;
    private $id = 1;
    private $responseHandlers = [];

    public function __construct(Stream $stream)
    {
        $this->stream = $stream;
    }

    public function request(string $rpcMethod, $args, callable $callable)
    {
        $id = 'id' . $this->getNextRequestId();
        $this->responseHandlers[$id] = $callable;
        try {
            $this->stream->send([self::TYPE_REQUEST, $rpcMethod, $args]);
        } catch (\Throwable $e) {
            $this->popResponseHandler($id);
            throw $e;
        }
    }

    public function notification(string $rpcMethod, $args)
    {
        $this->stream->send([self::TYPE_NOTIFICATION, $rpcMethod, $args]);
    }


    public function sessionStart(
        callable $onRequest,
        callable $onNotification
    ) {
        $messageHandler = new MessageHandler(
            $this->stream,
            $onRequest,
            $onNotification,
            [$this, 'handleResponse']
        );

        $this->stream->listen(function($message) use ($messageHandler){
            $messageHandler->handleMessage($message);
        });
    }

    public function stop()
    {
        $this->stream->stop();
    }

    public function handleResponse($id, $error, $response)
    {

    }


    private function getNextRequestId()
    {
        return $this->synchronized(function () {
            $id = $this->id;
            // I know about ++, increment does not work on threaded!
            $this->id = $id + 1;
            return $id;
        });
    }

    private function popResponseHandler($id)
    {
        return $this->synchronized(function ($id) {
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
