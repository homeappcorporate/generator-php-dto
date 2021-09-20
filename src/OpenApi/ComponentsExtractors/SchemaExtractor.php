<?php

declare(strict_types=1);

namespace Homeapp\OpenapiGenerator\OpenApi\ComponentsExtractors;

use Exception;
use Homeapp\OpenapiGenerator\Deffenition\ClassDefinitionData;
use Homeapp\OpenapiGenerator\NamespaceHelper;
use Homeapp\OpenapiGenerator\OpenApi\DefinitionExtractor\PropertyExtractor;
use Homeapp\OpenapiGenerator\PHP\ConstructorGenerator;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\Property;

class SchemaExtractor
{
    private const SUB_NAMESPACE = 'Schemas';
    private ConstructorGenerator $constructorGenerator;
    private NamespaceHelper $namespaceHelper;
    private PropertyExtractor $propertyExtractor;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(ConstructorGenerator $constructorGenerator, NamespaceHelper $namespaceHelper, PropertyExtractor $propertyExtractor, )
    {
        $this->constructorGenerator = $constructorGenerator;
        $this->namespaceHelper = $namespaceHelper;
        $this->propertyExtractor = $propertyExtractor;
    }

    /**
     * @throws Exception
     */
    public function extractSchema(string $schemaName, array $schema): ClassDefinitionData
    {
        [
            'type' => $type,
            'required' => $required,
            'description' => $description,
            'properties' => $properties,
            'allOf' => $allOf,
        ] = $schema;
        /**
         * @var list<string> $required
         * @var array<string, array> $properties
         * @var string|null $description
         * @var string $type
         */
        $class = new ClassType($schemaName);
        if ($type !== 'object') {
            if (is_array($allOf) && count($allOf) > 0) {
                foreach ($allOf as $subSchema) {
                    /** @var Property $property */
                    foreach ($this->extractProperties($subSchema) as $property) {
                        $class->setProperties()
                    }
                }
            }

            throw new Exception(sprintf('Type "%s" is not implemented. Schema name: %s', $type, $schemaName));
        }

        if ($description) {
            $class->addComment($description);
        }
        $construct = new Method('__construct');
        $construct->setPublic();
        $constructorProperties = [];
        foreach ($properties as $propertyName => $propertyStructure) {
            $property = $this->propertyExtractor->extractProperty($propertyName, $propertyStructure);
            if (in_array($property->getName(), $required)) {
                $constructorProperties[] = $property;
            }
            $class->addMember($property);
        }
        if (!empty($constructorProperties)) {
            $this->constructorGenerator->addContractorWithRequiredArgument($class, $constructorProperties);
        }
        return new ClassDefinitionData($class, $this->namespaceHelper->getNamespace(self::SUB_NAMESPACE), self::SUB_NAMESPACE);
    }

    /**
     * @param Property[] $subSchema
     * @throws Exception
     */
    private function extractProperties(array $subSchema): \Generator
    {
        ['type' => $type, 'properties' => $properties] = $subSchema;
        if ($type !== 'object') {
            throw new Exception(sprintf('SubType. Type "%s" is not implemented.', $type));
        }
        new ClassType();
        foreach ($properties as $propertyName => $propertyStructure) {
            yield $this->propertyExtractor->extractProperty( $propertyName, $propertyStructure);
        }
    }
}
