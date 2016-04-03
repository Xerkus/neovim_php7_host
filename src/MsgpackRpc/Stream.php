<?php
declare(strict_types=1);

namespace Xerkus\Neovim\MsgpackRpc;

use MessagePackUnpacker;
use Threaded;
use Xerkus\Neovim\MsgpackRpc\Stream\Server;

class Stream extends Threaded
{
    private $server;
    private $run = false;

    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    public function send($message)
    {
        $msg = $this->toNvim($message);
        $msg = \msgpack_pack($msg);
        $this->server->write($msg);
    }

    public function listen(callable $onMessage)
    {
        if ($this->run) {
            throw new RuntimeException('Stream is already listening for messages');
        }
        $this->run = true;
        $unpacker = new MessagePackUnpacker;
        while($this->run) {
            // Executed before reading and feeding data in case there is
            // message left from previous cycle
            if ($unpacker->execute()) {
                $onMessage($unpacker->data());
                // Extension have no docs, but from what i understood by reading
                // source, reset() should clear parsed part of data buffer.
                $unpacker->reset();
            }
            if (!$this->run) {
                // do not go into reading if stopping
                break;
            }
            $data = $this->server->read();
            $unpacker->feed($data);
        }
        $this->run = false;
    }

    public function stop()
    {
        $this->run = false;
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
