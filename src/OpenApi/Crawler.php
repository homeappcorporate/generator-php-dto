<?php

declare(strict_types=1);

namespace Homeapp\OpenapiGenerator\OpenApi;

use Exception;
use Homeapp\OpenapiGenerator\Deffenition\ClassDefinitionData;
use Homeapp\OpenapiGenerator\OpenApi\ComponentsExtractors\RequestBodyExtractor;
use Homeapp\OpenapiGenerator\OpenApi\ComponentsExtractors\ResponseExtractor;
use Homeapp\OpenapiGenerator\OpenApi\ComponentsExtractors\SchemaExtractor;
use Psr\Log\LoggerInterface;
use Traversable;

class Crawler
{
    private RequestBodyExtractor $requestBodyExtractor;
    private SchemaExtractor $schemaExtractor;
    private ResponseExtractor $responseExtractor;
    private LoggerInterface $logger;
    private ResponseClassExtractor $responseClassExtractor;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(RequestBodyExtractor $requestBodyExtractor, SchemaExtractor $schemaExtractor, ResponseExtractor $responseExtractor, LoggerInterface $logger, ResponseClassExtractor $responseClassExtractor)
    {
        $this->requestBodyExtractor = $requestBodyExtractor;
        $this->schemaExtractor = $schemaExtractor;
        $this->responseExtractor = $responseExtractor;
        $this->logger = $logger;
        $this->responseClassExtractor = $responseClassExtractor;
    }

    /**
     * @psalm-suppress MixedAssignment
     * @psalm-return Traversable<int, ClassDefinitionData>
     */
    public function walk(array $openapi): Traversable
    {
        [
            'paths' => $paths,
            'components' => [
                'responses' => $responses,
                'schemas' => $schemas,
                'requestBodies' => $requestBodies,
            ]] = $openapi;
        /**
         * @var array<string, array> $requestBodies
         * @var array<string, array> $responses
         * @var array<string, array> $schemas
         */

        $this->logger->debug('Crawling paths');
        foreach ($paths as $url => $verbs) {
            $this->logger->debug('Path: "{path}"', [
                'path' => $url
            ]);
            foreach ($verbs as $verbName => $verb) {
                $this->logger->debug('Verb: {verb}', [
                    'verb' => $verbName,
                    'path' => $url,
                ]);
                ['operationId' => $operationId, 'responses' => $responses] = $verb;
                $this->logger->debug('Operation: {operationId}', [
                    'operationId' => $operationId,
                ]);

                /**
                 * @var int $code
                 * @var  $response
                 */
                foreach ($responses as $code => $response) {
                    $this->logger->debug('Response: {code}', [
                        'code' => $code,
                    ]);
                    yield $this->responseClassExtractor->extractResponseClass($code, $operationId, $response, $openapi);
                }
                // TODO generate query parameters

            }
        }


        $this->logger->debug('Extracting definition from components');
        // Components
        // Schema should not be generated due to different set of properties for request and responses class
        // Each request/response/query parameters must have it's on class in it's own namespace
//        foreach ($schemas as $schemaName => $schema) {
//            yield $this->schemaExtractor->extractSchema($schemaName, $schema);
//        }

        if (is_array($responses)) {
            foreach ($responses as $responseName => $responseStructure) {
                yield $this->responseExtractor->extractResponse($responseName, $responseStructure);
            }
        }

        if (is_array($requestBodies)) {
            foreach ($requestBodies as $requestBodyName => $requestBody) {
                try {
                    yield $this->requestBodyExtractor->extractResponseBody($requestBodyName, $requestBody, $openapi);
                } catch (Exception $exception) {
                    $this->logger->error('Cannot create RequestBody "{requestBodyName}"', [
                        'requestBodyName' => $requestBodyName,
                        'requestBody' => $requestBody,
                        'exception' => (string)$exception,
                    ]);
                }
            }
        }
    }
}
