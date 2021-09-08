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
    private NamespaceHelper $namespaceHelper;
    private ConstructorGenerator $constructorGenerator;
    private PropertyExtractor $propertyExtractor;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(NamespaceHelper $namespaceHelper, ConstructorGenerator $constractorGenerator, PropertyExtractor $propertyExtractor)
    {
        $this->namespaceHelper = $namespaceHelper;
        $this->constructorGenerator = $constractorGenerator;
        $this->propertyExtractor = $propertyExtractor;
    }
    /**
     * @psalm-param array<string, array> $properties
     * @return ClassDefinitionData
     */
    public function extractClassesDefinition(string $className, string $subNamespace, ?string $description, array $properties): ClassDefinitionData
    {
        $class = new ClassType($className);
        if ($description !== null) {
            $class->addComment($description);
        }
        $construct = new Method('__construct');
        $construct->setPublic();
        foreach ($properties as $propertyName => $propertyStructure) {
            $property = $this->propertyExtractor->extractProperty($propertyName, $propertyStructure);
            $class->addMember($property);
        }
        $this->constructorGenerator->addContractorWithRequiredArgument($class, $class->getProperties());
        return new ClassDefinitionData($class, $this->namespaceHelper->getNamespace($subNamespace), $subNamespace);
    }
}
