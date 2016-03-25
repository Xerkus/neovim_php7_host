<?php
declare(strict_types=1);

namespace Xerkus\Neovim\Plugin;

use Xerkus\Neovim\Plugin\RpcHandler\RpcSpec;

final class Handler
{
    private $spec;
    private $callback;

    public function __construct(RpcSpec $spec, callable $callback)
    {
        $this->spec = $spec;
        $this->callback = $callback;
    }

    public function getSpec() : RpcSpec
    {
        return $this->spec;
    }

    public function getCallback() : callable
    {
        return $this->callback;
    }

    public function withCallback(callable $callback) : self
    {
        $new = clone($this);
        $new->callback = $callback;

        return $new;
    }
}
