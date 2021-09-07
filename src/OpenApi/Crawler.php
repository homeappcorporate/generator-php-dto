<?php

declare(strict_types=1);

namespace Homeapp\OpenapiGenerator\OpenApi;

use Homeapp\OpenapiGenerator\Deffenition\ClassDefinitionData;
use Homeapp\OpenapiGenerator\OpenApi\DefinitionExtractor\RequestBodyExtractor;
use Homeapp\OpenapiGenerator\OpenApi\DefinitionExtractor\ResponseExtractor;
use Homeapp\OpenapiGenerator\OpenApi\DefinitionExtractor\SchemaExtractor;
use Psr\Log\LoggerInterface;

class Crawler
{
    private RequestBodyExtractor $requestBodyExtractor;
    private SchemaExtractor $schemaExtractor;
    private ResponseExtractor $responseExtractor;
    private LoggerInterface $logger;

    public function __construct(RequestBodyExtractor $requestBodyExtractor, SchemaExtractor $schemaExtractor, ResponseExtractor $responseExtractor, LoggerInterface $logger)
    {
        $this->requestBodyExtractor = $requestBodyExtractor;
        $this->schemaExtractor = $schemaExtractor;
        $this->responseExtractor = $responseExtractor;
        $this->logger = $logger;
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
            'requestBodies' => $requestBodies,
        ]] = $openapi;


        $this->logger->debug('Extracting definition from components');
        // Components
        foreach ($schemas as $schemaName => $schema) {
            yield $this->schemaExtractor->extractSchema($schemaName, $schema);
        }

        foreach ($responses as $responseName => $responseStructure) {
            yield $this->responseExtractor->extractResponse($responseName, $responseStructure);
        }

        foreach ($requestBodies as $requestBodyName => $requestBody) {
            try {
                yield $this->requestBodyExtractor->extractResponseBody($requestBodyName, $requestBody, $openapi);
            } catch (\Exception $exception) {
                $this->logger->error('Cannot create RequestBody "{requestBodyName}"', [
                    'requestBodyName' => $requestBodyName,
                    'requestBody' => $requestBody,
                    'exception' => (string) $exception,
                ]);
            }
        }
    }
}
