<?php
declare(strict_types=1);

namespace Xerkus\Neovim\MsgpackRpc;

/**
 * This class exists for the sole reason: let Session avoid using properties
 * and as such avoid thread sharing handlers and by extension everything they
 * reference
 */
final class MessageHandler
{
    private $stream;
    private $requestHandler;
    private $notificationHandler;
    private $responseHandler;
    private $unknownTypeHandler;

    public function __construct(
        Stream $stream,
        callable $requestHandler,
        callable $notificationHandler,
        callable $responseHandler,
        callable $unknownTypeHandler = null
    ) {
        $this->stream = $stream;
        $this->requestHandler = $requestHandler;
        $this->notificationHandler = $notificationHandler;
        $this->responseHandler = $responseHandler;
        if (null === $unknownTypeHandler) {
            $unknownTypeHandler = [$this, 'handleUnknownType'];
        }
        $this->unknownTypeHandler = $unknownTypeHandler;
    }

    public function handleMessage(array $message)
    {
        $msg = print_r($message, true);
        fwrite(STDERR, $msg);
        try {
            switch ($message[0]) {
                case Session::TYPE_REQUEST:
                    $this->handleRequest($message);
                    break;
                case Session::onNotification:
                    $this->handleNotification($message);
                    break;
                case Session::TYPE_RESPONSE:
                    $this->handleResponse($message);
                    break;
                default:
                    ($this->unknownTypeHandler)($message);
            }
        } catch (\Exception $e) {
            // stack trace from host thread is useless
            $error = $e->getMessage();
            $this->stream->send([Session::TYPE_RESPONSE, 0, $error, null]);
            // rethrow exception. If it got here it is bad enough to stop host
            throw $e;
        }
    }

    private function handleRequest(array $message)
    {
        $id = $message[1];
        $rpcMethod = $message[2];
        $args = $message[3];
        ($this->requestHandler)(
            $rpcMethod,
            $args,
            new Response($this->stream, $id)
        );
    }

    private function handleNotification(array $message)
    {
        $rpcMethod = $message[1];
        $args = $message[2];
        ($this->notificationHandler)(
            $rpcMethod,
            $args
        );

    }

    private function handleResponse(array $message)
    {
        $id = $message[1];
        $error = $message[2];
        $result = $message[3];
        ($this->responseHandler)(
            $id,
            $error,
            $result
        );
    }

    private function handleUnknownType(array $message)
    {
        $error = 'Received invalid message: ' . print_r($message, true);
        $this->stream->send([self::TYPE_RESPONSE, 0, $error, null]);
    }
}
