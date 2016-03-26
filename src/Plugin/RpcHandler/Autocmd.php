<?php
declare(strict_types=1);

namespace Xerkus\Neovim\Plugin\RpcHandler;

use RuntimeException;

/**
 * @Annotation
 * @Target("METHOD")
 */
final class Autocmd implements RpcSpec
{
    use RpcSpecTrait;

    private $opts = [];

    /**
     *Array of values as passed from annotations reader
     *
     */
    public function __construct(array $values)
    {
        $name = $values['name'] ?? $values['value'] ?? null;
        if ($name === null) {
            throw new RuntimeException('Event name is required');
        }
        $this->setName($name);
        $this->setPattern($values['pattern'] ?? '*');
        $this->setSync($values['sync'] ?? false);
        $this->setEval($values['eval'] ?? null);
    }

    /**
     *  Create nvim function spec from positional parameters
     */
    public static function createAutocmd(
        string $name,
        string $pattern = '*',
        bool $sync = false,
        string $eval = null
    ) {
        $values = [
            'name' => $name,
            'pattern' => $pattern,
            'sync' => $sync,
            'eval' => $eval,
        ];
        return new self($values);
    }

    public function getType() : string
    {
        return 'autocmd';
    }

    protected function prepareOpts() : array
    {
        return $this->opts;
    }

    public function getShouldExport() : bool
    {
        return true;
    }

    private function setName(string $name)
    {
        if (empty($name)) {
            throw new RuntimeException('Invalid event name');
        }
        $this->name = $name;
    }

    private function setPattern(string $pattern)
    {
        if (empty($pattern)) {
            throw new RuntimeException('Invalid pattern');
        }
        $this->opts['pattern'] = $pattern;
    }

    private function setSync(bool $sync)
    {
        $this->sync = $sync;
    }

    private function setEval(string $eval = null)
    {
        if (!empty($eval)) {
            $this->opts['eval'] = $eval;
        }
    }
}
