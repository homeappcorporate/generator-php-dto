<?php

declare(strict_types=1);

namespace Homeapp\OpenapiGenerator\OpenApi\DefinitionExtractor;

use Homeapp\OpenapiGenerator\Command\CreateDTO;
use Homeapp\OpenapiGenerator\Deffenition\ClassDefinitionData;
use Homeapp\OpenapiGenerator\NamespaceHelper;
use Homeapp\OpenapiGenerator\OpenApi\RefFullClassNameConverter;
use Homeapp\OpenapiGenerator\OpenApi\TypeMapper;
use Homeapp\OpenapiGenerator\PHP\ConstructorGenerator;
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
    private ConstructorGenerator $constructorGenerator;
    private PropertyExtractor $propertyExtractor;

    public function __construct(TypeMapper $typeMapper, NamespaceHelper $namespaceHelper, RefFullClassNameConverter $refFullClassNameConverter, ConstructorGenerator $constractorGenerator, PropertyExtractor $propertyExtractor)
    {
        $this->typeMapper = $typeMapper;
        $this->namespaceHelper = $namespaceHelper;
        $this->refFullClassNameConverter = $refFullClassNameConverter;
        $this->constructorGenerator = $constractorGenerator;
        $this->propertyExtractor = $propertyExtractor;
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
            $property = $this->propertyExtractor->extractProperty($propertyName, $propertyStructure);
            $class->addMember($property);
        }
        $this->constructorGenerator->addContractorWithRequiredArgument($class, $class->getProperties());
        return new ClassDefinitionData($class, $this->namespaceHelper->getNamespace($subNamespace), $subNamespace);
    }
}
