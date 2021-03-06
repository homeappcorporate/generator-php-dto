<?php

declare(strict_types=1);

namespace Homeapp\OpenapiGenerator\OpenApi;

/**
 * @psalm-immutable
 */
class RefData
{
    public string $path;

    public function __construct(string $ref)
    {
        $this->path = str_replace('/', '.', substr($ref, 2));
    }
}
