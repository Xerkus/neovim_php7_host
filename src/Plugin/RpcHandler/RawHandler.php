<?php
declare(strict_types=1);

namespace Xerkus\Neovim\Plugin\RpcHandler;

use RuntimeException;

/**
 * @Annotation
 * @Target("METHOD")
 */
final class RawHandler implements RpcSpec
{
    private $name;
    private $sync = false;
    private $opts = [];
    private $pluginPath;

    /**
     *Array of values as passed from annotations reader
     *
     */
    public function __construct(array $values)
    {
        $name = $values['name'] ?? $values['value'] ?? null;
        if ($name === null) {
            throw new RuntimeException('Rpc handler name is required');
        }
        $this->setName($name);
        $this->setSync($values['sync'] ?? $this->sync);
    }

    /**
     *  Create nvim rpc handler spec from positional parameters
     */
    public static function createRawHandler(
        string $name,
        bool $sync = false
    ) {
        $values = [
            'name' => $name,
            'sync' => $sync,
        ];
        return new self($values);
    }

    public function getMethodName() : string
    {
        return $this->name;
    }

    public function getType() : string
    {
        return 'raw_handler';
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getIsSync() : bool
    {
        return $this->sync;
    }

    public function getOpts() : array
    {
        return $this->opts;
    }

    public function getSpecArray() : array
    {
        return [
            'type' => $this->getType(),
            'name' => $this->getName(),
            'sync' => $this->sync,
            'opts' => $this->opts,
        ];
    }

    public function getPluginPath()
    {
        return $this->pluginPath;
    }

    /**
     * Returns spec with plugin path $pluginPath
     */
    public function withPluginPath(string $pluginPath = null) : RpcSpec
    {
        $new = clone($this);
        $new->pluginPath = $pluginPath;

        return $new;
    }

    public function getShouldExport() : bool
    {
        return false;
    }

    private function setName(string $name)
    {
        if (empty($name)) {
            throw new RuntimeException('Invalid rpc handler name');
        }
        $this->name = $name;
    }

    private function setSync(bool $sync)
    {
        $this->sync = $sync;
    }
}
