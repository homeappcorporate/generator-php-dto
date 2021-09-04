<?php

namespace Homeapp\OpenapiGenerator\OpenApi\DefinitionExtractor;

use Homeapp\OpenapiGenerator\Command\CreateDTO;
use Homeapp\OpenapiGenerator\Deffenition\ClassDefinitionData;
use Homeapp\OpenapiGenerator\NamespaceHelper;
use Homeapp\OpenapiGenerator\OpenApi\TypeMapper;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\Parameter;

use Nette\PhpGenerator\Property;

use function array_key_exists;
use function DI\add;
use function sprintf;

class ObjectDefinitionExtractor
{
    private TypeMapper $typeMapper;
    private NamespaceHelper $namespaceHelper;

    public function __construct(TypeMapper $typeMapper, NamespaceHelper $namespaceHelper)
    {
        $this->typeMapper = $typeMapper;
        $this->namespaceHelper = $namespaceHelper;
    }

    /**
     * @param list<Property> $arguments
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
     * @return ClassDefinitionData
     */
    public function extractClassesDefinition(string $className, string $subNamespace, string $description, array $properties, array $required): ClassDefinitionData
    {
        $requiredMap = array_flip($required);
        $class = new ClassType($className);
        $class->addComment($description);
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
            $nullable = $nullable ?? false;
            $property->setType($this->typeMapper->map($type));
            $property->addComment($description);
            $property->setNullable($nullable);
            if ($nullable) {
                $property->setValue(null);
            }
            if (!$nullable || ($requiredMap[$propertyName] ?? null) !== null) {
                $requiredParameters[] = $property;
            }
        }
        $this->addContractorWithRequiredArgument($class, $requiredParameters);
        return new  ClassDefinitionData($class, $this->namespaceHelper->getNamespace($subNamespace));
    }
}