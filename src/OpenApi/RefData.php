<?php

declare(strict_types=1);

namespace Homeapp\OpenapiGenerator\OpenApi;

/**
 * @psalm-immutable
 */
class RefData
{
    public string $ref;
    public string $path;
    public string $name;

    public function __construct(string $ref)
    {
        $this->ref = $ref;
        $this->path = str_replace('/', '.', substr($ref, 2));
        $parts = explode('/', $ref);
        $this->name =end($parts);
    }
}
