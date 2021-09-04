<?php
declare(strict_types=1);

namespace Homeapp\OpenapiGenerator\Deffenition;

use Nette\PhpGenerator\ClassType;

class RequestBodyDefinition
{
    public string $xpath;
    public ClassType $class;

    /**
     * @param string $xpath
     * @param ClassType $class
     */
    public function __construct(string $xpath, ClassType $class)
    {
        $this->xpath = $xpath;
        $this->class = $class;
    }

}
