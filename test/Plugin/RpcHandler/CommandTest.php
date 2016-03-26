<?php
declare(strict_types=1);

namespace XerkusTest\Neovim\Plugin\RpcHandler;

use InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use RuntimeException;
use TypeError;
use Xerkus\Neovim\Plugin\RpcHandler\Command;

/**
 *
 * @coversDefaultClass Xerkus\Neovim\Plugin\RpcHandler\Command
 * @covers ::<!public>
 */
class CommandTest extends PHPUnit_Framework_TestCase
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
        $cmd = new Command([
            'name' => 'TestCommand',
            'sync' => true,
            'nargs' => '+',
            'complete' => 'dir',
            'range' => true,
            'count' => false,
            'bang' => true,
            'register' => true,
            'eval' => 'someEval()',
        ]);
        self::assertEquals('command', $cmd->getType());
        self::assertEquals('TestCommand', $cmd->getName());
        self::assertTrue($cmd->getIsSync());
        self::assertEquals([
            'type' => 'command',
            'name' => 'TestCommand',
            'sync' => true,
            'opts' => [
                'range' => true,
                'nargs' => '+',
                'complete' => 'dir',
                'range' => true,
                'bang' => true,
                'register' => true,
                'eval' => 'someEval()'
            ]
        ], $cmd->getSpecArray());
    }

    /**
     * @covers ::createCommand
     * @covers ::getType
     * @covers ::getName
     * @covers ::getIsSync
     * @covers ::getSpecArray
     */
    public function testNamedConstructorSetsAllValues()
    {
        $cmd = Command::createCommand(
            'TestCommand',
            true,
            '+',
            'dir',
            'N',
            false,
            true,
            true,
            'someEval()'
        );
        self::assertEquals('command', $cmd->getType());
        self::assertEquals('TestCommand', $cmd->getName());
        self::assertTrue($cmd->getIsSync());
        self::assertEquals([
            'type' => 'command',
            'name' => 'TestCommand',
            'sync' => true,
            'opts' => [
                'range' => true,
                'nargs' => '+',
                'complete' => 'dir',
                'range' => 'N',
                'bang' => true,
                'register' => true,
                'eval' => 'someEval()'
            ]
        ], $cmd->getSpecArray());
    }

    /**
     * @covers ::__construct
     */
    public function testCountAndRangeCantBeUsedTogether()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Options range and count are mutually exclusive');
        new Command([
            'name' => 'TestCommand',
            'range' => true,
            'count' => true,
        ]);
    }

    /**
     * @covers ::__construct
     */
    public function testCountIsSet()
    {
        $cmd = new Command(['name' => 'TestCommand', 'count' => 'N']);
        self::assertArrayHasKey('count', $cmd->getSpecArray()['opts']);
        self::assertEquals('N', $cmd->getSpecArray()['opts']['count']);
    }

    /**
     * @covers ::__construct
     */
    public function testNargsOutOfRangeValue()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp('/Nargs value can only be one of/');
        new Command([
            'name' => 'TestCommand',
            'nargs' => '$',
        ]);
    }

    /**
     * @covers ::getMethodName
     */
    public function testMethodNameIsCombinationOfTypeAndName()
    {
        $cmd = new Command(['name' => 'TestCommand']);
        self::assertEquals('command:TestCommand', $cmd->getMethodName());
    }

    /**
     * @covers ::__construct
     */
    public function testCommandNameMustBeString()
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessageRegExp('/must be of the type string/');
        new Command(['name' => true]);
    }

    /**
     * @covers ::__construct
     */
    public function testCommandNameMustNotBeEmpty()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid command name');
        new Command(['name' => '']);
    }

    /**
     * @covers ::__construct
     */
    public function testCommandNameIsRequired()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Command name is required');
        new Command([]);
    }

    /**
     * @covers ::__construct
     */
    public function testSyncMustBeBool()
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessageRegExp('/must be of the type bool/');
        new Command(['name' => 'TestCommand', 'sync' => 1]);
    }

    /**
     * @covers ::__construct
     */
    public function testEvalMustBeString()
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessageRegExp('/must be of the type string/');
        new Command(['name' => 'TestCommand', 'eval' => 1]);
    }

    /**
     * @covers ::__construct
     */
    public function testEvalIsNullable()
    {
        $cmd = new Command(['name' => 'TestCommand', 'eval' => null]);
        self::assertArrayNotHasKey('eval', $cmd->getSpecArray()['opts']);
    }

    /**
     * @covers ::getPluginPath
     */
    public function testPluginPathIsNullByDefault()
    {
        $cmd = new Command(['name' => 'TestCommand']);
        self::assertNull($cmd->getPluginPath());
    }

    /**
     * @covers ::getPluginPath
     * @covers ::withPluginPath
     */
    public function testWithPluginPathProducesNewCopy()
    {
        $cmd = new Command(['name' => 'TestCommand']);
        $new = $cmd->withPluginPath('/test/path/plugin.php');
        self::assertNotSame($new, $cmd);
        self::assertNull($cmd->getPluginPath());
        self::assertInstanceOf(Command::class, $new);
        self::assertEquals('/test/path/plugin.php', $new->getPluginPath());
    }

    /**
     * @covers ::getMethodName
     * @covers ::withPluginPath
     */
    public function testPluginPathIsPrefixedToMethodName()
    {
        $cmd = new Command(['name' => 'TestCommand']);
        $cmd = $cmd->withPluginPath('/test/path/plugin.php');
        self::assertEquals(
            '/test/path/plugin.php:command:TestCommand',
            $cmd->getMethodName()
        );
    }

    /**
     * @covers ::getShouldExport
     */
    public function testIsExportedToNeovim()
    {
        $cmd = new Command(['name' => 'TestCommand']);
        self::assertTrue($cmd->getShouldExport());
    }
}
