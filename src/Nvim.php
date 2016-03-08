<?php
declare(strict_types=1);

namespace Xerkus\Neovim;

/**
 * Represents remote nvim instance.
 *
 * Supposedly provides abstraction for nvim interactions and hides msgpack-rpc
 * remote api
 */
class Nvim
{
    /**
     * After this method is called, the client will receive redraw
     * notifications
     *
     */
    public function uiAttach($width, $height, $rgb)
    {
    }

    /**
     * Unregister as a remote UI
     *
     */
    public function uiDetach()
    {
    }

    /**
     * Notify nvim that the client window has resized
     *
     * If possible, nvim will send a redraw request to resize
     *
     */
    public function uiTryResize($width, $height)
    {
    }

    /**
     * Subscribe to nvim event
     *
     */
    public function subscribe($event)
    {
    }

    /**
     * Unsubscribe from a nvim event
     *
     */
    public function unsubscribe($event)
    {
    }

    /**
     * Execute a single ex command
     *
     * @param bool $async ignore response or any errors
     */
    public function command($command, $async = false)
    {
    }

    /**
     * Execute a single ex command and return the output
     *
     */
    public function commandOutput($command)
    {
    }

    /**
     * Evaluate a vimscript expression
     *
     * @param bool $async ignore response or any errors
     */
    public function eval($expression, $async)
    {
    }

    /**
     * Call a vimscript function
     *
     */
    public function call($name, ...$args)
    {
    }

    /**
     * Call a vimscript function, ignore response or any errors
     *
     * Note: separate method as php does not support named parameters
     *
     * @return void
     */
    public function callAsync($name, ...$args)
    {
    }

    /**
     * Return the number of display cells $string occupies
     * Tab is counted as one cell.
     *
     */
    public function strwidth($string)
    {
    }

    /**
     * Return a list of paths contained in the 'runtime' option
     *
     */
    public function listRuntimePaths()
    {
    }

    /**
     * Change nvim directory?
     *
     */
    public function chdir($directory)
    {
    }

    /**
     * Push keys to nvim user input buffer
     *
     * Options can be a string with the following character flags:
     *  - 'm': Remap keys. This is default.
     *  - 'n': Do not remap keys.
     *  - 't': Handle keys as if typed; otherwise they are handled as if coming
     *    from a mapping. This matters for undo, opening folds, etc.
     *
     */
    public function feedkeys($keys, $options, $escapeCsi)
    {
    }

    /**
     * Push `bytes` to Nvim low level input buffer.
     *
     * Unlike `feedkeys()`, this uses the lowest level input buffer and
     * the call is not deferred. It returns the number of bytes actually
     * written(which can be less than what was requested if the buffer
     * is full).
     *
     */
    public function input($bytes)
    {
    }

    /**
     * Replace any terminal code strings by byte sequences.
     *
     * The returned sequences are Nvim's internal representation of keys,
     * for example:
     * <esc> -> '\x1b'
     * <cr>  -> '\r'
     * <c-l> -> '\x0c'
     * <up>  -> '\x80ku'
     * The returned sequences can be used as input to feedkeys()
     *
     */
    public function replaceTermcodes($string, $fromPart = false, $doLt = true, $special = true)
    {
    }

    /**
     * Print $msg as an normal message
     *
     */
    public function outWrite(string $msg)
    {
    }

    /**
     * Print $msg as and error message
     *
     * @param bool $async ignore response or any errors
     */
    public function errWrite(string $msg, $async = false)
    {
    }
}
