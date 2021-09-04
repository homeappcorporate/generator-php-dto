<?php

declare(strict_types=1);

namespace Homeapp\OpenapiGenerator\OpenApi;

use Homeapp\OpenapiGenerator\Deffenition\ClassDefinitionData;
use Homeapp\OpenapiGenerator\OpenApi\DefinitionExtractor\ObjectDefinitionExtractor;
use Homeapp\OpenapiGenerator\OpenApi\DefinitionExtractor\RequestBodyExtractor;
use Homeapp\OpenapiGenerator\OpenApi\DefinitionExtractor\ResponseExtractor;
use Homeapp\OpenapiGenerator\OpenApi\DefinitionExtractor\SchemaExtractor;
use Homeapp\OpenapiGenerator\OperationNameConverter;

class Crawler
{
    private RequestBodyExtractor $requestBodyExtractor;
    private OperationNameConverter $nameConverter;
    private SchemaExtractor $schemaExtractor;
    private ResponseExtractor $responseExtractor;

    public function __construct(RequestBodyExtractor $requestBodyExtractor, OperationNameConverter $nameConverter, SchemaExtractor $schemaExtractor, ResponseExtractor $responseExtractor)
    {
        $this->requestBodyExtractor = $requestBodyExtractor;
        $this->nameConverter = $nameConverter;
        $this->schemaExtractor = $schemaExtractor;
        $this->responseExtractor = $responseExtractor;
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

        foreach ($responses as $responseName => $responseStructure) {
            yield $this->responseExtractor->extractResponse($responseName, $responseStructure);
        }


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




    }
}
