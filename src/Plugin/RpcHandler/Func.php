<?php
declare(strict_types=1);

namespace Xerkus\Neovim\Plugin\RpcHandler;

/**
 * @Annotation
 * @Target("METHOD")
 */
final class Func implements RpcSpec
{
    private $name;
    private $sync = false;
    private $opts = [
        'range' => false,
    ];
    private $pluginPath;

    /**
     *Array of values passed from annotation reader
     *
     */
    public function __construct(array $values)
    {
        $name = $values['name'] ?? ($values['value'] ?? null);
        if (!is_string($name) || empty($name)) {
            throw new \RuntimeException('Function name is required');
        }

        $this->name = $name;
    }

    public function getMethodName() : string
    {
        $method = $this->getType() . ':' . $this->name;
        if (!empty($this->pluginPath)) {
            $method = $this->pluginPath . ':' . $method;
        }
        return $method;
    }

    public function getType() : string
    {
        return 'function';
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
        return true;
    }
}
