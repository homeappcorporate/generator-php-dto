<?php

namespace Homeapp\OpenapiGenerator\OpenApi\ComponentsExtractors;

/**
 * @psalm-immutable
 */
class PropertyExtractorOptions
{
    public bool $addReadOnly;
    public bool $addWriteOnly;

    public function __construct(bool $addReadOnly, bool $addWriteOnly)
    {
        $this->addReadOnly = $addReadOnly;
        $this->addWriteOnly = $addWriteOnly;
    }

}