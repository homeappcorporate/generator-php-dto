<?php
declare(strict_types=1);

namespace Homeapp\OpenapiGenerator\OpenApi;

use Exception;
use Homeapp\OpenapiGenerator\Deffenition\ClassDefinitionData;
use Homeapp\OpenapiGenerator\NamespaceHelper;
use Homeapp\OpenapiGenerator\OpenApi\ComponentsExtractors\PropertyExtractorOptions;
use Homeapp\OpenapiGenerator\OpenApi\DefinitionExtractor\PropertyExtractor;
use Nette\PhpGenerator\ClassType;
use Psr\Log\LoggerInterface;

class ResponseClassExtractor
{
    private LoggerInterface $logger;
    private NamespaceHelper $namespaceHelper;
    private PropertyExtractor $propertyExtractor;
    private PropertyExtractorOptions $propertyExtractorOptions;

    public function __construct(LoggerInterface $logger, NamespaceHelper $namespaceHelper, PropertyExtractor $propertyExtractor)
    {
        $this->logger = $logger;
        $this->namespaceHelper = $namespaceHelper;
        $this->propertyExtractor = $propertyExtractor;
        $this->propertyExtractorOptions = new PropertyExtractorOptions(true, false);
    }

    /** @throws Exception */
    public function extractResponseClass(int $code, $operationId, array $response, array $openapi):ClassDefinitionData
    {
        $this->logger->debug('Trying to generated response', [
            'response' => $response
        ]);
        $class = new ClassType(
            $this->generateClassName($code, $operationId)
        );

        $definition = new ClassDefinitionData(
            $class,
            $this->namespaceHelper->getNamespace('Responses'),
            'Responses'
        );

        ['content' => ['application/json' => ['schema' => [
            'type' => $type,
            'description' => $description,
            '$ref' => $ref,
            'properties' => $properties,
        ]]]] = $response;
        if ($description !== null) {
            $class->addComment($description);
        }
        if (!array_key_exists('content', $response)){
            $this->logger->debug('empty response');
            return $definition;
        }

        if ($type === 'object') {
            $this->logger->debug('Generating from plain object');

            foreach ($properties as $propertyName => $property) {
                $property = $this->propertyExtractor->extractProperty($propertyName, $property, $this->propertyExtractorOptions);
                if ($property === null) {
                    continue;
                }
                $class->addMember($property);
            }
        } else {
            throw new Exception('Generation without type object is not supported');
        }


        return $definition;
    }

    public function generateClassName(int $code, string $operationId):string
    {
        $operation = ucwords($operationId, '-');
        $operation = str_replace('-', '', $operation);

        $result = sprintf('Response%s%s', $operation, $code);
        $this->logger->debug('Name "{responseName}" generated from code: {code} operationId: {operationId}', [
            'responseName' => $result,
            'code' => $code,
            'operationId' => $operationId,
        ]);
        return $result;
    }
}