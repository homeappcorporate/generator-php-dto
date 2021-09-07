<?php

declare(strict_types=1);

namespace Homeapp\OpenapiGenerator\Deffenition;

use Nette\PhpGenerator\ClassType;

/**
 * @psalm-immutable
 */
class ClassDefinitionData
{
    public ClassType $class;
    public string $namespace;
    public string $subNamespace;

    /**
     * @param ClassType $class
     * @param string $fullNamespace
     */
    public function __construct(ClassType $class, string $fullNamespace, string $subNamespace)
    {
        $this->class = $class;
        $this->namespace = $fullNamespace;
        $this->subNamespace = $subNamespace;
    }
}
