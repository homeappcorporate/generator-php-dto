<?php

declare(strict_types=1);

namespace Homeapp\OpenapiGenerator\OpenApi;

use Homeapp\OpenapiGenerator\ClassDefinitionRegister;
use Homeapp\OpenapiGenerator\Deffenition\ClassDefinitionData;
use Homeapp\OpenapiGenerator\ArrayPathFetcher;
use Nette\PhpGenerator\ClassType;

class RefClassGenerator
{
    private ArrayPathFetcher $fetcher;
    private ClassDefinitionRegister $register;

    public function __construct(ArrayPathFetcher $fetcher, ClassDefinitionRegister $register)
    {
        $this->fetcher = $fetcher;
        $this->register = $register;
    }

    public function generateClassByRef(RefData $ref, array $openapi): ClassDefinitionData
    {
        $definition = $this->fetcher->getContent($ref->path, $openapi);
        $class = new ClassType($ref->name);

        dd($definition);
        $this->register->register($ref->path);
        return new ClassDefinitionData();
    }
}
