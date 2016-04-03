<?php
declare(strict_types=1);

namespace Xerkus\Neovim\MsgpackRpc\Stream;

use Threaded;

class StdServer extends Threaded implements Server
{
    public function read($timeout = false)
    {
        // @TODO handle errors
        // @TODO use wrapper? and set blocking/timeouts
        return $this->synchronized(function () {
            return fread(STDIN, 1024);
        });
    }

    public function write($msg)
    {
        // @TODO handle errors
        $this->synchronized(function ($msg) {
            fwrite(STDOUT, $msg, strlen($msg));
        }, $msg);
    }

    public function __destruct()
    {
        // @TODO destroy stdin/stdout wrappers, but only in creating thread
        // and only if not referenced anywhere else
        // Failure to do so will lead to nasty results. heap
        // corruption, segfaults, who know else
    }
}
