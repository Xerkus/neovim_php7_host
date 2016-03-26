<?php
declare(strict_types=1);

namespace Xerkus\Neovim\Plugin\RpcHandler;

use RuntimeException;
use InvalidArgumentException;

/**
 * @Annotation
 * @Target("METHOD")
 */
final class Command implements RpcSpec
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
            throw new RuntimeException('Command name is required');
        }
        if (!empty($values['range']) && !empty($values['count'])) {
            throw new RuntimeException('Options range and count are mutually exclusive');
        }

        $this->setName($name);
        $this->setSync($values['sync'] ?? false);
        $this->setNargs($values['nargs'] ?? '0');
        $this->setComplete($values['complete'] ?? null);
        $this->setRange($values['range'] ?? false);
        $this->setCount($values['count'] ?? false);
        $this->setBang($values['bang'] ?? false);
        $this->setRegister($values['register'] ?? false);
        $this->setEval($values['eval'] ?? null);
    }

    /**
     *  Create nvim function spec from positional parameters
     *  @var $range string|bool
     *  @var $count string|bool
     */
    public static function createCommand(
        string $name,
        bool $sync = false,
        string $nargs = '0',
        string $complete = null,
        $range = false,
        $count = false,
        bool $bang = false,
        bool $register = false,
        string $eval = null
    ) {
        $values = [
            'name' => $name,
            'sync' => $sync,
            'nargs' => $nargs,
            'complete' => $complete,
            'range' => $range,
            'count' => $count,
            'bang' => $bang,
            'register' => $register,
            'eval' => $eval,
        ];
        return new self($values);
    }

    public function getType() : string
    {
        return 'command';
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
            throw new RuntimeException('Invalid command name');
        }
        $this->name = $name;
    }

    private function setSync(bool $sync)
    {
        $this->sync = $sync;
    }

    private function setNargs(string $nargs)
    {
        $validValues = ['0', '1', '*', '?', '+'];

        if (!in_array($nargs, $validValues)) {
            throw new InvalidArgumentException(
                'Nargs value can only be one of ' . implode(', ', $validValues)
            );
        }
        $this->opts['nargs'] = $nargs;
    }

    private function setComplete(string $complete = null)
    {
        if ($complete === null) {
            return;
        }
        $this->opts['complete'] = $complete;
    }

    private function setRange($range)
    {
        if ($range === false) {
            return;
        }
        // bool or string
        $this->opts['range'] = $range;
    }

    private function setCount($count)
    {
        if ($count === false) {
            return;
        }
        //bool or string
        $this->opts['count'] = $count;
    }

    private function setBang(bool $bang)
    {
        $bang && $this->opts['bang'] = true;
    }


    private function setRegister(bool $register)
    {
        $this->opts['register'] = $register;
    }

    private function setEval(string $eval = null)
    {
        if (!empty($eval)) {
            $this->opts['eval'] = $eval;
        }
    }
}
