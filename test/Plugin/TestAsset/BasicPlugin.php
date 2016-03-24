<?php
declare(strict_types=1);

namespace XerkusTest\Neovim\Plugin\TestAsset;

use Xerkus\Neovim\Plugin\Plugin;
use Xerkus\Neovim\Nvim;

abstract class BasicPlugin implements Plugin
{
    public $nvim;

    public function __construct(Nvim $nvim = null)
    {
        $this->nvim = $nvim;
    }

    public function getName() : string
    {
        return get_class($this);
    }

    public function getRpcHandlers() : array
    {
        return [];
    }

    public function onShutdown()
    {
    }
}
