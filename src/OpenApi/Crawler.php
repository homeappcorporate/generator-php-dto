<?php

declare(strict_types=1);

namespace Homeapp\OpenapiGenerator\OpenApi;

use Homeapp\OpenapiGenerator\Deffenition\ClassDefinitionData;
use Homeapp\OpenapiGenerator\OpenApi\DefinitionExtractor\ObjectDefinitionExtractor;
use Homeapp\OpenapiGenerator\OpenApi\DefinitionExtractor\RequestBodyExtractor;
use Homeapp\OpenapiGenerator\OpenApi\DefinitionExtractor\SchemaExtractor;
use Homeapp\OpenapiGenerator\OperationNameConverter;

class Crawler
{
    private ObjectDefinitionExtractor $classDefinitionExtractor;
    private RequestBodyExtractor $requestBodyExtractor;
    private OperationNameConverter $nameConverter;
    private SchemaExtractor $schemaExtractor;

    public function __construct(ObjectDefinitionExtractor $classDefinitionExtractor, RequestBodyExtractor $requestBodyExtractor, OperationNameConverter $nameConverter, SchemaExtractor $schemaExtractor)
    {
        $this->classDefinitionExtractor = $classDefinitionExtractor;
        $this->requestBodyExtractor = $requestBodyExtractor;
        $this->nameConverter = $nameConverter;
        $this->schemaExtractor = $schemaExtractor;
    }

    /**
     * @psalm-return \Traversable<int, ClassDefinitionData>
     */
    public function walk(array $openapi): \Traversable
    {
        [
            'paths' => $paths,
            'components' => [
            'responses' => $responses,
            'schemas' => $schemas,
        ]] = $openapi;

        foreach ($schemas as $schemaName => $schema) {
            yield $this->schemaExtractor->extractSchema($schemaName, $schema);
        }
//
//        foreach ($schemas as $schemaName => $schema) {
//            yield $this->schemaExtractor->extractSchema($schemaName, $schema);
//        }
//

//        foreach ($paths as $path => $pathData) {
//            foreach ($pathData as $method => $methodData) {
//                ['operationId' => $operationId] = $methodData;
//                $operationName = $this->nameConverter->convert($operationId);
//                yield $this->requestBodyExtractor->generateRequestBodyDeffenition(
//                    $operationName,
//                    "paths.$path.$method.requestBody",
//                    $openapi
//                );
//            }
//        }





//        foreach ($responses as $responseName => $responseStructure) {
//            foreach ($this->extractClassesFromResponse($responseName, $responseStructure) as $definition) {
//                yield $definition;
//            }
//        }
    }

//    /**
//     * @param string $responseName
//     * @param array $stracture
//     * @return \Traversable<int, ClassDefinitionData>
//     */
//    private function extractClassesFromResponse(string $responseName, array $stracture): \Traversable
//    {
//        $schema = $stracture['content']['application/json']['schema'];
//        foreach ($this->classDefinitionExtractor->extractClassesDefinition(
//            $responseName,
//            'Responses',
//            $stracture['description'],
//            $schema['properties'],
//        ) as $class) {
//            yield $class;
//        }
//    }
}
