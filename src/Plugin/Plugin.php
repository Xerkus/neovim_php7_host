<?php
declare(strict_types=1);

namespace Xerkus\Neovim\Plugin;

use Xerkus\Neovim\Nvim;

/**
 * Plugin marker interface.
 */
interface Plugin
{
    /**
     * Human readable plugin name
     */
    public function getName() : string;

    /**
     * Returns rpc handlers
     *
     * @return Handler[]
     */
    public function getRpcHandlers() : array;

    /**
     * Shutdown callback, nvim might be gone by the time it is called
     */
    public function onShutdown();
}
