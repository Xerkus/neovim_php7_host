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
     * Rpc type
     */
    public function getType() : string;

    /**
     * Name of the function, command or autocommand, etc
     */
    public function getName() : string;

    /**
     * Neovim is blocking on sync-ed rpc calls.
     *
     * Async calls are not blocking but they ignore any return values or errors
     *
     * Note to implementors: blocking call does not mean plugin can not receive
     * nested calls. Be carefull not to cause deadlock
     */
    public function getIsSync() : bool;

    /**
     * Full rpc method name, usually "{$pluginPath}:{$type}:{$name}"
     */
    public function getMethodName() : string;

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
     * Path of the plugin exposing this handler.
     *
     * Path is injected by plugin container, any value set by plugin itself
     * will not be used
     *
     * @return string|null Plugin path if porvided or null
     */
    public function getPluginPath();

    /**
     * Returns copy of spec object with plugin path
     */
    public function withPluginPath(string $pluginPath = null) : self;
}
