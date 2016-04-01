<?php
declare(strict_types=1);

namespace Xerkus\Neovim\MsgpackRpc;

use Threaded;
use Xerkus\Neovim\MsgpackRpc\Session\ThreadedRequestTracker;

class Session
{
    private $stream;
    private $requestTracker;

    public function __construct(Stream $stream)
    {
        $this->stream = $stream;
        $this->requestTracker = new ThreadedRequestTracker;
    }

    public function run(
        Callable $onRequest,
        Callable $onNotification,
        Callable $onSetup = null,
        Callable $onError = null
    ) {
        $onSetup();
        $this->stream->nextMessage([$this, 'onMessage']);
    }

    private function onMessage($message)
    {
        $msg = print_r($message, true);
        fwrite(STDERR, $msg);
    }
}
