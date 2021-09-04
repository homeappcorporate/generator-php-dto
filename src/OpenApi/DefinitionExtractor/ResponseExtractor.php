<?php

declare(strict_types=1);

namespace Homeapp\OpenapiGenerator\OpenApi\DefinitionExtractor;

use Homeapp\OpenapiGenerator\Deffenition\ClassDefinitionData;
use Exception;

class ResponseExtractor
{
    private ObjectDefinitionExtractor $objectDefinitionExtractor;

    public function __construct(ObjectDefinitionExtractor $objectDefinitionExtractor)
    {
        $this->objectDefinitionExtractor = $objectDefinitionExtractor;
    }

    /**
     * @throws Exception
     */
    public function extractResponse(string $responseName, array $response): ClassDefinitionData
    {
        [
            'description' => $description,
            'content' => [
                'application/json' => [
                    'schema' => $schema,
                ],
            ],
        ] = $response;
        if ($schema['type'] !== 'object') {
            throw new Exception(sprintf('Type "%s" is not implemented', $schema['type']));
        }
        [
            'required' => $required,
            'properties' => $properties,
        ] = $schema;

        return $this->objectDefinitionExtractor->extractClassesDefinition($responseName, 'Responses', $description, $properties, $required);
    }
}
