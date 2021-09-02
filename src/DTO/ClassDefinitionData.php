<?php

namespace Homeapp\OpenapiGenerator\DTO;

use Nette\PhpGenerator\ClassType;

/**
 * @psalm-immutable
 */
class ClassDefinitionData
{
    public ClassType $class;
    public string $namespace;

    /**
     * @param ClassType $class
     * @param string $namespace
     */
    public function __construct(ClassType $class, string $namespace)
    {
        $this->class = $class;
        $this->namespace = $namespace;
    }

}