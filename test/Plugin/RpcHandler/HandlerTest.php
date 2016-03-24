<?php
declare(strict_types=1);

namespace XerkusTest\Neovim\Plugin\RpcHandler;

use PHPUnit_Framework_TestCase;
use Xerkus\Neovim\Plugin\RpcHandler\Handler;
use Xerkus\Neovim\Plugin\RpcHandler\RpcSpec;

/**
 *
 * @coversDefaultClass Xerkus\Neovim\Plugin\RpcHandler\Handler
 * @covers ::<!public>
 */
class HandlerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::getCallback()
     * @covers ::getSpec()
     */
    public function testSpecAndCbAreProperlySet()
    {
        $spec = $this->getMockWithoutInvokingTheOriginalConstructor(RpcSpec::class);
        $cb = function () {
        };
        $handler = new Handler($spec, $cb);

        self::assertSame($spec, $handler->getSpec());
        self::assertSame($cb, $handler->getCallback());
    }

    /**
     * @covers ::withCallback()
     */
    public function testWithCallbackProducesNewCopy()
    {
        $spec = $this->getMockWithoutInvokingTheOriginalConstructor(RpcSpec::class);
        $cb = function () {
        };
        $handler = new Handler($spec, $cb);

        $cb2 = function () {
        };
        $handler2 = $handler->withCallback($cb2);

        self::assertNotSame($handler2, $handler);
        self::assertSame($cb2, $handler2->getCallback());
    }

    /**
     * @covers ::withCallback()
     */
    public function testWithCallbackDoesNotChangeHandler()
    {
        $spec = $this->getMockWithoutInvokingTheOriginalConstructor(RpcSpec::class);
        $cb = function () {
        };
        $handler = new Handler($spec, $cb);

        $cb2 = function () {
        };

        $handler2 = $handler->withCallback($cb2);

        self::assertSame($cb, $handler->getCallback());
        self::assertNotSame($cb2, $handler->getCallback());
    }
}
