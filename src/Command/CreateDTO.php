<?php declare(strict_types=1);

namespace Homeapp\OpenapiGenerator\Command;

use Namespaced\Bar;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpNamespace;
use Nette\PhpGenerator\Printer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function Couchbase\defaultDecoder;

class CreateDTO extends Command
{
    private Printer $printer;

    public function __construct(string $name = null, Printer $printer)
    {
        parent::__construct($name);
        $this->printer = $printer;
    }

    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'generator:create-dto';
    private string $namespace = 'Generated';
    private string $output = './out';

    protected function configure(): void
    {
        $this
            ->setDescription('Generate class from json')
            ->addArgument('path-json', InputArgument::REQUIRED, 'From which class will be generated');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path =  $input->getArgument('path-json');
        $json = json_decode(file_get_contents($path), true);

        $classes = [];
        $responses = $json['components']['responses'];
        foreach ($responses as $responseName => $responseStructure) {
            $class = new ClassType($responseName);
            $classes[] = $class;

            $class->addComment($responseStructure['description']);
            $schema = $responseStructure['content']['application/json']['schema'];
            $properties = $schema['properties'];
            $construct = new Method('__construct');
            $construct->setPublic();
            foreach ($properties as $propertyName => $propertyStructure) {
                if(array_key_exists('$ref', $propertyStructure)) {
                    // TODO implement
                    continue;
                }
                $property = $class->addProperty($propertyName);
                ['type' =>  $type, 'nullable' => $nullable, 'description' => $description] = $propertyStructure;
                $property->setType($type);
                $property->addComment($description);
                $property->setNullable($nullable);
                if (!$nullable) {
                    $construct->addParameter($propertyName)
                        ->setNullable($nullable)
                        ->setType($type);
                }
            }
            foreach ($construct->getParameters() as $parameter) {
                $name = $parameter->getName();
                $body .= sprintf('$this->%s = $%s;' . PHP_EOL, $name, $name);
            }
            $construct->setBody($body);
            $class->addMember($construct);

        }
        foreach ($classes as $class) {
            echo $this->printer->setTypeResolving(true)->printClass($class, new PhpNamespace($this->namespace)) . PHP_EOL;
        }

        return Command::SUCCESS;

    }
}