<?php

namespace Homeapp\OpenapiGenerator\PHP;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Property;

class ConstructorGenerator
{

    /**
     * @param list<Property> $arguments
     */
    public function addContractorWithRequiredArgument(ClassType $class, array $arguments): void
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
}