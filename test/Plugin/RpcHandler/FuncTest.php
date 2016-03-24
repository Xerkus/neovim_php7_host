<?php

namespace XerkusTest\Neovim\Plugin\RpcHandler;

use PHPUnit_Framework_TestCase;
use Xerkus\Neovim\Plugin\RpcHandler\Func;

/**
 *
 * @coversDefaultClass Xerkus\Neovim\Plugin\RpcHandler\Func
 * @covers ::<!public>
 */
class FuncTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getSpecArray
     */
    public function testSpecWithFuncNameOnly()
    {
        $func = new Func(['name' => 'TestFunc']);
        self::assertEquals('TestFunc', $func->getName());
        self::assertEquals('function:TestFunc', $func->getMethodName());
        self::assertEquals([
            'type' => 'function',
            'name' => 'TestFunc',
            'sync' => false,
            'opts' => ['range' => false]
        ], $func->getSpecArray());
    }
}
