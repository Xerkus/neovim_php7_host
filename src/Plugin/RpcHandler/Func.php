<?php
declare(strict_types=1);

namespace Xerkus\Neovim\Plugin\RpcHandler;

use RuntimeException;

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
     *Array of values as passed from annotations reader
     *
     */
    public function __construct(array $values)
    {
        $name = $values['name'] ?? $values['value'] ?? null;
        if ($name === null) {
            throw new RuntimeException('Function name is required');
        }
        $this->setName($name);
        $this->setSync($values['sync'] ?? $this->sync);
        $this->setRange($values['range'] ?? false);
        $this->setEval($values['eval'] ?? null);
    }

    /**
     *  Create nvim function spec from positional parameters
     */
    public static function createFunction(
        string $name,
        bool $sync = false,
        bool $range = false,
        string $eval = null
    ) {
        $values = [
            'name' => $name,
            'sync' => $sync,
            'range' => $range,
            'eval' => $eval,
        ];
        return new self($values);
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

    private function setName(string $name)
    {
        if (empty($name)) {
            throw new RuntimeException('Invalid function name');
        }
        $this->name = $name;
    }

    private function setSync(bool $sync)
    {
        $this->sync = $sync;
    }

    private function setRange(bool $range)
    {
        $this->opts['range'] = $range;
    }

    private function setEval(string $eval = null)
    {
        unset($this->opts['eval']);
        if (!empty($eval)) {
            $this->opts['eval'] = $eval;
        }
    }
}
