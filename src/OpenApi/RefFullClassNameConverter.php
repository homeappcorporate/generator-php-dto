<?php

declare(strict_types=1);

namespace Homeapp\OpenapiGenerator\OpenApi;

use Homeapp\OpenapiGenerator\NamespaceHelper;

class RefFullClassNameConverter
{
    private NamespaceHelper $namespaceHelper;

    public function __construct(NamespaceHelper $namespaceHelper)
    {
        $this->namespaceHelper = $namespaceHelper;
    }

    public function convertRefToFullClassName(string $ref): string
    {
        $rest = substr($ref, strlen('#/components/'));
        $parts = explode('/', $rest);
        $parts = array_map('ucfirst', $parts);
        return $this->namespaceHelper->getClassReference($parts[0], $parts[1]);
    }
}
