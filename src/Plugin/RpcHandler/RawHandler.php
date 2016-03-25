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
    use RpcSpecTrait;

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
        $this->setSync($values['sync'] ?? false);
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
        return $this->getName();
    }

    public function getType() : string
    {
        return 'raw_handler';
    }

    protected function prepareOpts() : array
    {
        return [];
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
