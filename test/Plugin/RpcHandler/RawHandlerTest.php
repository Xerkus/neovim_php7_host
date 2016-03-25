<?php
declare(strict_types=1);

namespace XerkusTest\Neovim\Plugin\RpcHandler;

use PHPUnit_Framework_TestCase;
use RuntimeException;
use TypeError;
use Xerkus\Neovim\Plugin\RpcHandler\RawHandler;

/**
 *
 * @coversDefaultClass Xerkus\Neovim\Plugin\RpcHandler\RawHandler
 * @covers ::<!public>
 */
class RawHandlerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getType
     * @covers ::getName
     * @covers ::getIsSync
     */
    public function testAllValuesAreProperlySet()
    {
        $raw = new RawHandler([
            'name' => 'TestHandler',
            'sync' => true
        ]);
        self::assertEquals('raw_handler', $raw->getType());
        self::assertEquals('TestHandler', $raw->getName());
        self::assertTrue($raw->getIsSync());
    }

    /**
     * @covers ::createRawHandler
     * @covers ::getType
     * @covers ::getName
     * @covers ::getIsSync
     */
    public function testNamedConstructorSetsAllValues()
    {
        $raw = RawHandler::createRawHandler('TestHandler', true);
        self::assertEquals('raw_handler', $raw->getType());
        self::assertEquals('TestHandler', $raw->getName());
        self::assertTrue($raw->getIsSync());
    }

    /**
     * @covers ::__construct
     * @covers ::getSpecArray
     */
    public function testRpcSpecArray()
    {
        $raw = new RawHandler([
            'name' => 'TestHandler',
            'sync' => true
        ]);
        self::assertEquals([
            'type' => 'raw_handler',
            'name' => 'TestHandler',
            'sync' => true,
            'opts' => []
        ], $raw->getSpecArray());
    }

    /**
     * @covers ::getMethodName
     */
    public function testMethodNameIsEqualToName()
    {
        $raw = new RawHandler(['name' => 'TestHandler']);
        self::assertEquals('TestHandler', $raw->getMethodName());
    }

    /**
     * @covers ::__construct
     */
    public function testHandlerNameMustBeString()
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessageRegExp('/must be of the type string/');
        new RawHandler(['name' => true]);
    }

    /**
     * @covers ::__construct
     */
    public function testHandlerNameMustNotBeEmpty()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid rpc handler name');
        new RawHandler(['name' => '']);
    }

    /**
     * @covers ::__construct
     */
    public function testHandlerNameIsRequired()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Rpc handler name is required');
        new RawHandler([]);
    }

    /**
     * @covers ::__construct
     */
    public function testSyncMustBeBool()
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessageRegExp('/must be of the type bool/');
        new RawHandler(['name' => 'TestHandler', 'sync' => 1]);
    }

    /**
     * @covers ::getPluginPath
     */
    public function testPluginPathIsNullByDefault()
    {
        $raw = new RawHandler(['name' => 'TestHandler']);
        self::assertNull($raw->getPluginPath());
    }

    /**
     * @covers ::getPluginPath
     * @covers ::withPluginPath
     */
    public function testWithPluginPathProducesNewCopy()
    {
        $raw = new RawHandler(['name' => 'TestHandler']);
        $new = $raw->withPluginPath('/test/path/plugin.php');
        self::assertNotSame($new, $raw);
        self::assertNull($raw->getPluginPath());
        self::assertInstanceOf(RawHandler::class, $new);
        self::assertEquals('/test/path/plugin.php', $new->getPluginPath());
    }

    /**
     * @covers ::getMethodName
     * @covers ::withPluginPath
     */
    public function testPluginPathIsNotPrefixedToMethodName()
    {
        $raw = new RawHandler(['name' => 'TestHandler']);
        $raw = $raw->withPluginPath('/test/path/plugin.php');
        self::assertEquals('TestHandler', $raw->getMethodName());
    }

    /**
     * @covers ::getShouldExport
     */
    public function testRawHandlerIsNotExportedToNeovim()
    {
        $raw = new RawHandler(['name' => 'TestHandler']);
        self::assertFalse($raw->getShouldExport());
    }
}
