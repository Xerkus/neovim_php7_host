<?php
declare(strict_types=1);

namespace Xerkus\Neovim\MsgpackRpc;

interface Connection
{
    public function read($timeout = false);

    public function write($msg);
}
