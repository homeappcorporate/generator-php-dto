<?php

declare(strict_types=1);

namespace Homeapp\OpenapiGenerator\OpenApi\DefinitionExtractor;

use Homeapp\OpenapiGenerator\Deffenition\RequestBodyDefinition;
use Homeapp\OpenapiGenerator\NamespaceHelper;
use Homeapp\OpenapiGenerator\ArrayPathFetcher;
use Homeapp\OpenapiGenerator\OpenApi\RefClassGenerator;
use Homeapp\OpenapiGenerator\OpenApi\RefData;
use Nette\PhpGenerator\ClassType;
use Psr\Log\LoggerInterface;

class RequestBodyExtractor
{
    private ArrayPathFetcher $path;
    private LoggerInterface $logger;
    private RefClassGenerator $refClassGenerator;

    public function __construct(ArrayPathFetcher $path, LoggerInterface $logger, RefClassGenerator $refClassGenerator)
    {
        $this->path = $path;
        $this->logger = $logger;
        $this->refClassGenerator = $refClassGenerator;
    }


    public function generateRequestBodyDeffenition(string $operationName, string $xpath, array $openapi): RequestBodyDefinition
    {
        $requestBody = $this->path->getContent($xpath, $openapi);
        if (($ref = $requestBody['$ref'] ?? null) !== null) {
            $ref = new RefData($ref);
            $this->refClassGenerator->generateClassByRef($ref, $openapi);
        }

        $this->logger->error('Generating requestBody without $ref is not supported', [
            'xpath' => $xpath,
        ]);
        return new RequestBodyDefinition($xpath, new ClassType());
    }
}
