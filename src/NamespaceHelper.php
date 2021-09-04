<?php

declare(strict_types=1);

namespace Homeapp\OpenapiGenerator;

class NamespaceHelper
{
    private string $globalNamespace;

    public function __construct(string $globalNamespace = 'DefaultNamespace')
    {
        $this->globalNamespace = $globalNamespace;
    }

    public function getClassReference(string $subNamespace, string $className): string
    {
        return sprintf('%s\\%s\\%s', $this->globalNamespace, $subNamespace, $className);
    }

    public function getNamespace(string $subNamespace): string
    {
        return sprintf('%s\\%s', $this->globalNamespace, $subNamespace);
    }

    public function setGlobalNamespace(string $globalNamespace): void
    {
        $this->globalNamespace = $globalNamespace;
    }
}
