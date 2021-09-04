<?php

namespace Homeapp\OpenapiGenerator\OpenApi;

use Homeapp\OpenapiGenerator\Command\CreateDTO;
use Homeapp\OpenapiGenerator\Deffenition\ClassDefinitionData;
use Homeapp\OpenapiGenerator\OpenApi\TypeMapper;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\Parameter;

class PHPClassDefinitionExtractor
{
    private TypeMapper $typeMapper;
    private array $mapExtractedClasses = [];
    private string $globalNamespace = '';

    public function __construct(TypeMapper $typeMapper)
    {
        $this->typeMapper = $typeMapper;
    }

    public function setGlobalNamespace(string $namespace):void
    {
        $this->globalNamespace = $namespace;
    }

    /**
     * @param Parameter[] $arguments
     */
    private function addContractorWithRequiredArgument(ClassType $class, array $arguments): void
    {
        $construct = $class->addMethod('__construct');
        $body = '';
        foreach ($arguments as $parameter) {
            $name = $parameter->getName();
            $body .= sprintf('$this->%s = $%s;' . PHP_EOL, $name, $name);
            $construct->addParameter($name)
                ->setNullable($parameter->isNullable())
                ->setType($parameter->getType());
        }
        $construct->setBody($body);
        $class->addMember($construct);
    }

    /**
     * @return \Traversable<int, ClassDefinitionData>
     */
    public function extractClassesDefinition(string $className, string $subNamespace, string $description, array $openAPIProperties): \Traversable
    {
        $classes = [];
        $class = new ClassType($className);
        $class->addComment($description);
        $properties = $openAPIProperties;
        $construct = new Method('__construct');
        $construct->setPublic();
        $requiredParameters = [];
        foreach ($properties as $propertyName => $propertyStructure) {
            if (array_key_exists('$ref', $propertyStructure)) {
//                $this->extractSchemaDefinition($propertyStructure['$ref']);
                // TODO implement
                continue;
            }
            $property = $class->addProperty($propertyName);
            ['type' => $type, 'nullable' => $nullable, 'description' => $description] = $propertyStructure;
            $property->setType($this->typeMapper->map($type));
            $property->addComment($description);
            $property->setNullable($nullable);
            if (!$nullable) {
                $requiredParameters[] = $property;
            }
        }

        $this->addContractorWithRequiredArgument($class, $requiredParameters);
        yield new  ClassDefinitionData($class, $this->getFullNamespace($subNamespace));
    }

    private function alreadyExtracted(string $namespace, string $class):bool
    {
        return array_key_exists(sprintf('%s\%s', $namespace, $class), $this->mapExtractedClasses);
    }
    private function markAsExtracted(string $namespace, string $class):void
    {
        $this->mapExtractedClasses[sprintf('%s\%s', $namespace, $class)] = true;
    }

    private function getFullNamespace(string $namespace):string
    {
        return sprintf('%s%s', $this->globalNamespace, $namespace);
    }
}