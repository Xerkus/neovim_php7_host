<?php
declare(strict_types=1);

namespace Example;

use Doctrine\Common\Annotations\AnnotationReader;
use Xerkus\Neovim\Nvim;
use Xerkus\Neovim\Plugin\Plugin;
use Xerkus\Neovim\Plugin\RpcHandler as Rpc;
use Xerkus\Neovim\Plugin\RpcHandler\AnnotatedBuilder;

final class SamplePlugin implements Plugin
{
    private $nvim;
    private $count = 0;

    public function __construct(Nvim $nvim)
    {
        $this->nvim = $nvim;
    }

    public function getName() : string
    {
        return __CLASS__;
    }

    public function getRpcHandlers() : array
    {
        $reader = new AnnotationReader;
        return (new AnnotatedBuilder($reader))->getAnnotatedHandlers($this);
    }

    /**
     * @Rpc\Command("Cmd", range="", nargs="*", sync=true)
     */
    public function commandHandler($args, $range)
    {
        $this->incrementCalls();
        //self.vim.current.line = (
        //        'Command: Called %d times, args: %s, range: %s' % (self.calls,
        //                                                           args,
        //                                                           range))
    }

    /**
     * @Rpc\Autocmd(
     *     "BufEnter",
     *     pattern="*.php",
     *     eval="expand(\<afile>\")",
     *     sync=true
     * )
     */
    public function autocmdHandler($filename)
    {
        $this->incrementCalls();
        // self.vim.current.line = (
        //        'Autocmd: Called %s times, file: %s' % (self.calls, filename))
    }

    /**
     * @Rpc\Func("Func")
     */
    public function functionHandler($args)
    {
        $this->incrementCalls();
        //    self.vim.current.line = (
        //        'Function: Called %d times, args: %s' % (self.calls, args))
    }

    private function incrementCalls()
    {
        if ($this->count >= 5) {
            throw new \RuntimeException('Too many calls!');
        }
        $this->count++;
    }

    public function onShutdown()
    {
        // noop
    }
}
