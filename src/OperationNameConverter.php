<?php

declare(strict_types=1);

namespace Homeapp\OpenapiGenerator;

class OperationNameConverter
{
    public function convert(string $operationId): string
    {
        return str_replace('-', '', ucwords($operationId, '-'));
    }
}
