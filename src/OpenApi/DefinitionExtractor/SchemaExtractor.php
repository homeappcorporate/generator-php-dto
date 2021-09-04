<?php

declare(strict_types=1);

namespace Homeapp\OpenapiGenerator\OpenApi\DefinitionExtractor;

use Homeapp\OpenapiGenerator\Deffenition\ClassDefinitionData;
use Exception;

class SchemaExtractor
{
    private ObjectDefinitionExtractor $objectDefinitionExtractor;

    public function __construct(ObjectDefinitionExtractor $objectDefinitionExtractor)
    {
        $this->objectDefinitionExtractor = $objectDefinitionExtractor;
    }

    /**
     * @throws Exception
     */
    public function extractSchema(string $schemaName, array $schema): ClassDefinitionData
    {
        [
            'type' => $type,
            'required' => $required,
            'description' => $description,
            'properties' => $properties
        ] = $schema;
        if ($type !== 'object') {
            throw new Exception(sprintf('Type "%s" is not implemented', $type));
        }
        return $this->objectDefinitionExtractor->extractClassesDefinition($schemaName, 'Schemas', $description, $properties, $required);
    }
}