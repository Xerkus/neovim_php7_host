<?php
declare(strict_types=1);

namespace Xerkus\Neovim\Plugin\RpcHandler;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use ReflectionClass;
use ReflectionMethod;
use Xerkus\Neovim\Plugin\Handler;
use Xerkus\Neovim\Plugin\Plugin;

final class AnnotatedBuilder
{
    /**
     *
     * @var AnnotationReader
     */
    private $reader;

    public function __construct(AnnotationReader $reader)
    {
        $this->reader = $reader;
        // horrible hack, remove it as soon as v2 is out
        // @codeCoverageIgnoreStart
        AnnotationRegistry::registerLoader(function ($class) {
            return class_exists($class, true);
        });
        // @codeCoverageIgnoreEnd
    }

    public function getAnnotatedHandlers(Plugin $plugin) : array
    {
        $handlers = [];
        $reflection = new ReflectionClass($plugin);
        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            $methodName = $method->getName();
            // skip magic methods
            if (substr($methodName, 0, 2) === '__') {
                continue;
            }

            $annotations = $this->reader->getMethodAnnotations($method);
            foreach ($annotations as $annotation) {
                if (!$annotation instanceof RpcSpec) {
                    continue;
                }
                $handlers[] = new Handler($annotation, [$plugin, $methodName]);
            }
        }

        return $handlers;
    }
}
