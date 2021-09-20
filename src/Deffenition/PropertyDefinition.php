<?php

namespace Homeapp\OpenapiGenerator\Deffenition;

use Nette\PhpGenerator\Property;

/**
 * @psalm-immutable
 */
class PropertyDefinition
{
    public bool $required;
    public Property $definition;
}