<?php
declare(strict_types=1);

namespace Xerkus\Neovim\MsgpackRpc;

use MessagePackUnpacker;

class Stream
{
    private $server;

    public function __construct(Server $server)
    {
        $this->server = $server;
        $this->packer = new MessagePackUnpacker;
    }

    public function send($message)
    {
        $msg = $this->toNvim($message);
        $msg = $this->packer->pack($msg);
        $this->server->send($msg);
    }

    public function run(Callable $onMessage)
    {
        $this->server->run([$this, 'onData']);
    }

    public function stop()
    {
        $this->server->stop();
    }

    public function nextMessage($timeout = false)
    {
        //if ($msg = $this->packer->data()) {
        //
        //}

        //@TODO figure what happens if data have two messages? message and chunk?
        $start = time();
        while(!$this->packer->execute()) {
            // @TODO try-catch and reset packer?
            $data = $this->server->read($timeout);
            if (empty($data)) {
                return;
            }
            $this->packer->feed($data);
        }
        $msg = $this->packer->data();
        return $this->fromNvim($msg);
    }

    private function fromNvim($message)
    {
        return $message;
    }

    private function toNvim($message)
    {
        return $message;
    }
}
