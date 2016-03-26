<?php

namespace XerkusTest\Neovim\Plugin\RpcHandler;

use PHPUnit_Framework_TestCase;
use RuntimeException;
use TypeError;
use Xerkus\Neovim\Plugin\RpcHandler\Autocmd;

/**
 *
 * @coversDefaultClass Xerkus\Neovim\Plugin\RpcHandler\Autocmd
 * @covers ::<!public>
 */
class AutocmdTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getType
     * @covers ::getName
     * @covers ::getIsSync
     * @covers ::getSpecArray
     */
    public function testAllValuesAreProperlySet()
    {
        $aucmd = new Autocmd([
            'name' => 'TestEvent',
            'pattern' => '*.php',
            'sync' => true,
            'eval' => 'someEval()',
        ]);
        self::assertEquals('autocmd', $aucmd->getType());
        self::assertEquals('TestEvent', $aucmd->getName());
        self::assertTrue($aucmd->getIsSync());
        self::assertEquals([
            'type' => 'autocmd',
            'name' => 'TestEvent',
            'sync' => true,
            'opts' => [
                'pattern' => '*.php',
                'eval' => 'someEval()'
            ]
        ], $aucmd->getSpecArray());
    }

    /**
     * @covers ::createAutocmd
     * @covers ::getType
     * @covers ::getName
     * @covers ::getIsSync
     * @covers ::getSpecArray
     */
    public function testNamedConstructorSetsAllValues()
    {
        $aucmd = Autocmd::createAutocmd('TestEvent', '*.php', true, 'someEval()');
        self::assertEquals('autocmd', $aucmd->getType());
        self::assertEquals('TestEvent', $aucmd->getName());
        self::assertTrue($aucmd->getIsSync());
        self::assertEquals([
            'type' => 'autocmd',
            'name' => 'TestEvent',
            'sync' => true,
            'opts' => [
                'pattern' => '*.php',
                'eval' => 'someEval()'
            ]
        ], $aucmd->getSpecArray());
    }

    /**
     * @covers ::getMethodName
     */
    public function testMethodNameIsCombinationOfTypeAndName()
    {
        $aucmd = new Autocmd(['name' => 'TestEvent']);
        self::assertEquals('autocmd:TestEvent', $aucmd->getMethodName());
    }

    /**
     * @covers ::__construct
     */
    public function testEventNameMustBeString()
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessageRegExp('/must be of the type string/');
        new Autocmd(['name' => true]);
    }

    /**
     * @covers ::__construct
     */
    public function testEventNameMustNotBeEmpty()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid event name');
        new Autocmd(['name' => '']);
    }

    /**
     * @covers ::__construct
     */
    public function testEventNameIsRequired()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Event name is required');
        new Autocmd([]);
    }

    /**
     * @covers ::__construct
     */
    public function testSyncMustBeBool()
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessageRegExp('/must be of the type bool/');
        new Autocmd(['name' => 'TestEvent', 'sync' => 1]);
    }

    /**
     * @covers ::__construct
     */
    public function testPatternMustNotBeEmpty()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid pattern');
        new Autocmd(['name' => 'TestEvent', 'pattern' => '']);
    }

    /**
     * @covers ::__construct
     */
    public function testEvalMustBeString()
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessageRegExp('/must be of the type string/');
        new Autocmd(['name' => 'TestEvent', 'eval' => 1]);
    }

    /**
     * @covers ::__construct
     */
    public function testEvalIsNullable()
    {
        $aucmd = new Autocmd(['name' => 'TestEvent', 'eval' => null]);
        self::assertArrayNotHasKey('eval', $aucmd->getSpecArray()['opts']);
    }

    /**
     * @covers ::getPluginPath
     */
    public function testPluginPathIsNullByDefault()
    {
        $aucmd = new Autocmd(['name' => 'TestEvent']);
        self::assertNull($aucmd->getPluginPath());
    }

    /**
     * @covers ::getPluginPath
     * @covers ::withPluginPath
     */
    public function testWithPluginPathProducesNewCopy()
    {
        $aucmd = new Autocmd(['name' => 'TestEvent']);
        $new = $aucmd->withPluginPath('/test/path/plugin.php');
        self::assertNotSame($new, $aucmd);
        self::assertNull($aucmd->getPluginPath());
        self::assertInstanceOf(Autocmd::class, $new);
        self::assertEquals('/test/path/plugin.php', $new->getPluginPath());
    }

    /**
     * @covers ::getMethodName
     * @covers ::withPluginPath
     */
    public function testPluginPathIsPrefixedToMethodName()
    {
        $aucmd = new Autocmd(['name' => 'TestEvent']);
        $aucmd = $aucmd->withPluginPath('/test/path/plugin.php');
        self::assertEquals(
            '/test/path/plugin.php:autocmd:TestEvent',
            $aucmd->getMethodName()
        );
    }

    /**
     * @covers ::getShouldExport
     */
    public function testIsExportedToNeovim()
    {
        $aucmd = new Autocmd(['name' => 'TestEvent', 'eval' => null]);
        self::assertTrue($aucmd->getShouldExport());
    }
}
