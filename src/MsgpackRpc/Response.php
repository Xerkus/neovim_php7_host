<?php
declare(strict_types=1);

namespace Xerkus\Neovim\MsgpackRpc;

class Response
{
    private $stream;
    private $id;

    public function __construct(Stream $stream, $id)
    {
        $this->stream = $stream;
        $this->id = $id;
    }

    public function send($reply)
    {
        $this->stream->send([Session::TYPE_RESPONSE, $this->id, null, $reply]);
    }

    public function error($error)
    {
        $this->stream->send([Session::TYPE_RESPONSE, $this->id, $error, null]);
    }
}
