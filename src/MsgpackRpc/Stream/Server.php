<?php
declare(strict_types=1);

namespace Xerkus\Neovim\MsgpackRpc\Stream;

interface Server
{
    public function read($timeout = false);

    public function write($msg);
}
