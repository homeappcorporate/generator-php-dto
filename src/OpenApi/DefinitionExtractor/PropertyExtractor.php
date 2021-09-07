<?php

namespace Homeapp\OpenapiGenerator\OpenApi\DefinitionExtractor;

use Homeapp\OpenapiGenerator\OpenApi\RefFullClassNameConverter;
use Homeapp\OpenapiGenerator\OpenApi\TypeMapper;
use Nette\PhpGenerator\Property;

class PropertyExtractor
{
    private RefFullClassNameConverter $refFullClassNameConverter;
    private TypeMapper $typeMapper;

    public function __construct(RefFullClassNameConverter $refFullClassNameConverter, TypeMapper $typeMapper)
    {
        $this->refFullClassNameConverter = $refFullClassNameConverter;
        $this->typeMapper = $typeMapper;
    }

    public function extractProperty(string $propertyName, array $propertyStructure): Property
    {
        $property =  new Property($propertyName);
        ['nullable' => $nullable, 'description' => $description, '$ref' => $ref, 'type' => $type, 'default' => $default] = $propertyStructure;
        $nullable = $nullable ?? false;
        if ($description !== null) {
            $property->addComment($description);
        }
        if (is_string($ref)) {
            $refClassName = $this->refFullClassNameConverter->convertRefToFullClassName($ref);
            $property->setType($refClassName);
            $property->setNullable(false);
            return $property;
        }
        $property->setType($this->typeMapper->map($type));
        $property->setNullable($nullable);
        if ($default === 'null') {
            $property->setValue(null);
        }
        return $property;
    }
}