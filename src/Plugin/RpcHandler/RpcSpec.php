<?php
declare(strict_types=1);

namespace Xerkus\Neovim\Plugin\RpcHandler;

/**
 * Rpc handler that announces itself to neovim.
 *
 */
interface RpcSpec
{
    /**
     * Rpc method name, usually "{$path}:{$type}:{$name}"
     */
    public function getMethodName() : string;

    /**
     * Rpc type
     */
    public function getType() : string;

    /**
     * Name of the function, command or autocommand, etc
     */
    public function getName() : string;

    /**
     * Sync-ed rpc calls are blocking neovim
     *
     * Async call does not block and ignores return value or errors
     *
     * Note to implementors: blocking call does not mean plugin can not receive
     * nested calls. Be carefull not to cause deadlock
     */
    public function getIsSync() : bool;

    /**
     * List of Rpc options
     */
    public function getOpts() : array;

    /**
     * Rpc handler spec as array prepared for export to neovim
     *
     * Returns array with keys 'type', 'name', 'sync' and 'opts'
     */
    public function getSpecArray() : array;

    /**
     * Controls where rpc handler should be announced to neovim
     *
     * Currently only 'command', 'autocmd' and 'function' types are supported
     * by neovim and exported on 'specs' request
     */
    public function getShouldExport() : bool;

    /**
     *
     */
    public function getPluginPath();

    /**
     * Returns spec with plugin path $pluginPath
     */
    public function withPluginPath(string $pluginPath = null) : self;
}
