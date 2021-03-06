<?php
declare(strict_types=1);

namespace XerkusTest\Neovim\Plugin\RpcHandler;

use PHPUnit_Framework_TestCase;
use RuntimeException;
use TypeError;
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
     * @covers ::getType
     * @covers ::getName
     * @covers ::getIsSync
     */
    public function testAllValuesAreProperlySet()
    {
        $func = new Func([
            'name' => 'TestFunc',
            'sync' => true,
            'range' => true,
            'eval' => 'someEval()',
        ]);
        self::assertEquals('function', $func->getType());
        self::assertEquals('TestFunc', $func->getName());
        self::assertTrue($func->getIsSync());
    }

    /**
     * @covers ::createFunction
     * @covers ::getType
     * @covers ::getName
     * @covers ::getIsSync
     */
    public function testNamedConstructorSetsAllValues()
    {
        $func = Func::createFunction('TestFunc', true, true, 'someEval()');
        self::assertEquals('function', $func->getType());
        self::assertEquals('TestFunc', $func->getName());
        self::assertTrue($func->getIsSync());
    }

    /**
     * @covers ::__construct
     * @covers ::getSpecArray
     */
    public function testRpcSpecArray()
    {
        $func = new Func([
            'name' => 'TestFunc',
            'sync' => true,
            'range' => true,
            'eval' => 'someEval()',
        ]);
        self::assertEquals([
            'type' => 'function',
            'name' => 'TestFunc',
            'sync' => true,
            'opts' => ['range' => true, 'eval' => 'someEval()']
        ], $func->getSpecArray());
    }

    /**
     * @covers ::getMethodName
     */
    public function testMethodNameIsCombinationOfTypeAndName()
    {
        $func = new Func(['name' => 'TestFunc']);
        self::assertEquals('function:TestFunc', $func->getMethodName());
    }

    /**
     * @covers ::__construct
     */
    public function testFunctionNameMustBeString()
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessageRegExp('/must be of the type string/');
        new Func(['name' => true]);
    }

    /**
     * @covers ::__construct
     */
    public function testFunctionNameMustNotBeEmpty()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid function name');
        new Func(['name' => '']);
    }

    /**
     * @covers ::__construct
     */
    public function testFunctionNameIsRequired()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Function name is required');
        new Func([]);
    }

    /**
     * @covers ::__construct
     */
    public function testSyncMustBeBool()
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessageRegExp('/must be of the type bool/');
        new Func(['name' => 'TestFunc', 'sync' => 1]);
    }

    /**
     * @covers ::__construct
     */
    public function testRangeMustBeBool()
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessageRegExp('/must be of the type bool/');
        new Func(['name' => 'TestFunc', 'range' => 1]);
    }

    /**
     * @covers ::__construct
     */
    public function testEvalMustBeString()
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessageRegExp('/must be of the type string/');
        new Func(['name' => 'TestFunc', 'eval' => 1]);
    }

    /**
     * @covers ::__construct
     */
    public function testEvalIsNullable()
    {
        $func = new Func(['name' => 'TestFunc', 'eval' => null]);
        self::assertArrayNotHasKey('eval', $func->getSpecArray()['opts']);
    }

    /**
     * @covers ::getPluginPath
     */
    public function testPluginPathIsNullByDefault()
    {
        $func = new Func(['name' => 'TestFunc']);
        self::assertNull($func->getPluginPath());
    }

    /**
     * @covers ::getPluginPath
     * @covers ::withPluginPath
     */
    public function testWithPluginPathProducesNewCopy()
    {
        $func = new Func(['name' => 'TestFunc']);
        $new = $func->withPluginPath('/test/path/plugin.php');
        self::assertNotSame($new, $func);
        self::assertNull($func->getPluginPath());
        self::assertInstanceOf(Func::class, $new);
        self::assertEquals('/test/path/plugin.php', $new->getPluginPath());
    }

    /**
     * @covers ::getMethodName
     * @covers ::withPluginPath
     */
    public function testPluginPathIsPrefixedToMethodName()
    {
        $func = new Func(['name' => 'TestFunc']);
        $func = $func->withPluginPath('/test/path/plugin.php');
        self::assertEquals(
            '/test/path/plugin.php:function:TestFunc',
            $func->getMethodName()
        );
    }

    /**
     * @covers ::getShouldExport
     */
    public function testFunctionIsExportedToNeovim()
    {
        $func = new Func(['name' => 'TestFunc', 'eval' => null]);
        self::assertTrue($func->getShouldExport());
    }
}
