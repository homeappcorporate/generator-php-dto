<?php

namespace Homeapp\OpenapiGenerator\OpenApi\DefinitionExtractor;

use Homeapp\OpenapiGenerator\Command\CreateDTO;
use Homeapp\OpenapiGenerator\Deffenition\ClassDefinitionData;
use Homeapp\OpenapiGenerator\NamespaceHelper;
use Homeapp\OpenapiGenerator\OpenApi\RefFullClassNameConverter;
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
    private RefFullClassNameConverter $refFullClassNameConverter;

    public function __construct(TypeMapper $typeMapper, NamespaceHelper $namespaceHelper, RefFullClassNameConverter $refFullClassNameConverter)
    {
        $this->typeMapper = $typeMapper;
        $this->namespaceHelper = $namespaceHelper;
        $this->refFullClassNameConverter = $refFullClassNameConverter;
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
            $property = $class->addProperty($propertyName);
            if (array_key_exists('$ref', $propertyStructure)) {
                $refClassName = $this->refFullClassNameConverter->convertRefToFullClassName($propertyStructure['$ref']);
                $property->setType($refClassName);
                ['nullable' => $nullable, 'description' => $description] = $propertyStructure;
                $nullable = $nullable ?? false;
                if ($description !== null) {
                    $property->addComment($description);
                }
                $property->setNullable($nullable);
                if ($nullable) {
                    $property->setValue(null);
                }
                if (!$nullable || ($requiredMap[$propertyName] ?? false)) {
                    $requiredParameters[] = $property;
                }
//                $this->extractSchemaDefinition($propertyStructure['$ref']);
                // TODO implement
                continue;
            }
            ['type' => $type, 'nullable' => $nullable, 'description' => $description] = $propertyStructure;
            $nullable = $nullable ?? true;
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
        return new ClassDefinitionData($class, $this->namespaceHelper->getNamespace($subNamespace), $subNamespace);
    }
}
