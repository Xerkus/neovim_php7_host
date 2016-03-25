<?php
declare(strict_types=1);

namespace Xerkus\Neovim\Plugin\RpcHandler;

/**
 * Rpc handler that announces itself to neovim.
 *
 */
trait RpcSpecTrait
{
    private $name;
    private $sync;
    private $pluginPath;

    /**
     * @inheritDoc
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getIsSync() : bool
    {
        return $this->sync;
    }

    /**
     * @inheritDoc
     */
    public function getMethodName() : string
    {
        $method = $this->getType() . ':' . $this->getName();
        $pluginPath = $this->getPluginPath();
        if (!empty($pluginPath)) {
            $method = $pluginPath . ':' . $method;
        }
        return $method;
    }

    /**
     * @inheritDoc
     */
    public function getSpecArray() : array
    {
        return [
            'type' => $this->getType(),
            'name' => $this->getName(),
            'sync' => $this->getIsSync(),
            'opts' => $this->prepareOpts()
        ];
    }

    /**
     * Prepare opts array for spec array
     */
    abstract protected function prepareOpts() : array;

    /**
     * @inheritDoc
     */
    public function getPluginPath()
    {
        return $this->pluginPath;
    }

    /**
     * @inheritDoc
     */
    public function withPluginPath(string $pluginPath = null) : RpcSpec
    {
        $new = clone($this);
        $new->pluginPath = $pluginPath;

        return $new;
    }
}
