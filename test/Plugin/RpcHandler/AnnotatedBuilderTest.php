<?php
declare(strict_types=1);

namespace XerkusTest\Neovim\Plugin\RpcHandler;

use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit_Framework_TestCase;
use XerkusTest\Neovim\Plugin\TestAsset\BasicPlugin;
use Xerkus\Neovim\Plugin\RpcHandler as Rpc;
use Xerkus\Neovim\Plugin\RpcHandler\AnnotatedBuilder;

/**
 *
 * @coversDefaultClass Xerkus\Neovim\Plugin\RpcHandler\AnnotatedBuilder
 * @covers ::<!public>
 */
class AnnotatedBuilderTest extends PHPUnit_Framework_TestCase
{
    private $builder;

    public function setUp()
    {
        $annotationReader = new AnnotationReader;
        $this->builder = new AnnotatedBuilder($annotationReader);
    }

    /**
     * @covers ::getAnnotatedHandlers
     */
    public function testAnnotatedHandler()
    {
        $plugin = new class extends BasicPlugin
        {
            /**
             *
             * @Rpc\Func("TestFunc")
             */
            public function handler()
            {
            }
        };

        $handlers = $this->builder->getAnnotatedHandlers($plugin);
        self::assertCount(1, $handlers);
        self::assertInstanceOf(Rpc\Func::class, $handlers[0]->getSpec());
        self::assertEquals([$plugin, 'handler'], $handlers[0]->getCallback());
    }

    /**
     * @covers ::getAnnotatedHandlers
     */
    public function testMultipleAnnotatedHandlers()
    {
        $plugin = new class extends BasicPlugin
        {
            /**
             *
             * @Rpc\Func("TestFunc")
             */
            public function handler()
            {
            }

            /**
             *
             * @Rpc\Func("AnotherFunc")
             */
            public function handler1()
            {
            }
        };

        $handlers = $this->builder->getAnnotatedHandlers($plugin);
        self::assertCount(2, $handlers);
        self::assertEquals('TestFunc', $handlers[0]->getSpec()->getName());
        self::assertEquals([$plugin, 'handler'], $handlers[0]->getCallback());
        self::assertEquals('AnotherFunc', $handlers[1]->getSpec()->getName());
        self::assertEquals([$plugin, 'handler1'], $handlers[1]->getCallback());
    }

    /**
     * @covers ::getAnnotatedHandlers
     */
    public function testMultipleAnnotatedHandlersOnOneMethod()
    {
        $plugin = new class extends BasicPlugin
        {
            /**
             *
             * @Rpc\Func("TestFunc")
             * @Rpc\Func("AnotherFunc")
             */
            public function handler()
            {
            }
        };

        $handlers = $this->builder->getAnnotatedHandlers($plugin);
        self::assertCount(2, $handlers);
        self::assertEquals('TestFunc', $handlers[0]->getSpec()->getName());
        self::assertEquals([$plugin, 'handler'], $handlers[0]->getCallback());
        self::assertEquals('AnotherFunc', $handlers[1]->getSpec()->getName());
        self::assertEquals([$plugin, 'handler'], $handlers[1]->getCallback());
    }

    /**
     *
     * @covers ::getAnnotatedHandlers
     */
    public function testUnrelatedAnnotationsAreIgnored()
    {
        $plugin = new class extends BasicPlugin
        {
            /**
             *
             * @\Doctrine\Common\Annotations\Annotation\Target("ALL")
             * @Rpc\Func("TestFunc")
             */
            public function handler()
            {
            }
        };

        $handlers = $this->builder->getAnnotatedHandlers($plugin);
        self::assertCount(1, $handlers);
        self::assertInstanceOf(Rpc\Func::class, $handlers[0]->getSpec());
    }

    /**
     *
     * @covers ::getAnnotatedHandlers
     */
    public function testUnannotatedPluginDoesNotProduceHandlers()
    {
        $plugin = new class extends BasicPlugin
        {
            public function handler()
            {
            }
        };

        $handlers = $this->builder->getAnnotatedHandlers($plugin);
        self::assertEmpty($handlers);
    }

    /**
     * @covers ::getAnnotatedHandlers
     */
    public function testAnnotatedMagicMethodsAreIgnored()
    {
        $plugin = new class extends BasicPlugin
        {
            /**
             *
             * @Rpc\Func("TestFunc")
             */
            public function __magic()
            {
            }
        };

        $handlers = $this->builder->getAnnotatedHandlers($plugin);
        self::assertEmpty($handlers);
    }

    public function tearDown()
    {
        $this->builder = null;
    }
}
