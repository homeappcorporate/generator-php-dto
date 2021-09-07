<?php

declare(strict_types=1);

namespace Homeapp\OpenapiGenerator\OpenApi\ComponentsExtractors;

use Exception;
use Homeapp\OpenapiGenerator\ArrayPathFetcher;
use Homeapp\OpenapiGenerator\Deffenition\ClassDefinitionData;
use Homeapp\OpenapiGenerator\NamespaceHelper;
use Homeapp\OpenapiGenerator\OpenApi\RefData;
use Nette\PhpGenerator\ClassType;
use Psr\Log\LoggerInterface;

class RequestBodyExtractor
{
    private const NAMESPACE = 'RequestBodies';
    private ArrayPathFetcher $path;
    private LoggerInterface $logger;
    private ArrayPathFetcher $fetcher;
    private NamespaceHelper $namespaceHelper;

    public function __construct(ArrayPathFetcher $path, LoggerInterface $logger, ArrayPathFetcher $fetcher, NamespaceHelper $namespaceHelper)
    {
        $this->path = $path;
        $this->logger = $logger;
        $this->fetcher = $fetcher;
        $this->namespaceHelper = $namespaceHelper;
    }

    /**
     * @throws Exception
     */
    public function extractResponseBody(string $requestBodyName, array $requestBody, array $openapi): ClassDefinitionData
    {
        ['description' => $description, 'content' => ['application/json' => [
            'schema' => [
                '$ref' => $ref
            ]
        ]]] = $requestBody;
        if (!is_string($ref)) {
            throw new Exception('Creating response body without ref to schema is not supported');
        }
        $ref = new RefData($ref);
        $data = $this->fetcher->getContent($ref->path, $openapi);

        $class = new ClassType();
        $class->setName($requestBodyName);
        return  new ClassDefinitionData(
            $class,
            $this->namespaceHelper->getNamespace( self::NAMESPACE),
            self::NAMESPACE
        );
    }
}
