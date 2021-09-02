<?php declare(strict_types=1);

namespace Homeapp\OpenapiGenerator\Command;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\Parameter;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\Printer;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateDTO extends Command
{
    private Printer $printer;

    public function __construct(string $name = null, Printer $printer)
    {
        parent::__construct($name);
        $this->printer = $printer;
    }

    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'create-dto';
    private string $namespaceForResponses = 'Generated\\Responses';

    protected function configure(): void
    {
        $this
            ->setDescription('Generate class from json')
            ->addArgument('path-json', InputArgument::REQUIRED, 'From which class will be generated')
            ->addArgument('path-out', InputArgument::OPTIONAL, 'Where put generated content', sprintf('%s/out', getcwd()));
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path =  $input->getArgument('path-json');
        $json = json_decode(file_get_contents($path), true);
        $outputDirectory = $input->getArgument('path-out');

        $responses = $json['components']['responses'];
        $classesFromResponses = $this->extractClassesFromResponses($responses);
        foreach ($classesFromResponses as $class) {
            $this->generateClassFile($class, 'Responses', $outputDirectory);
        }
        return Command::SUCCESS;
    }


    /**
     * @param Parameter[] $arguments
     */
    protected function addContractorWithRequiredArgument(ClassType $class, array $arguments): void
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
     * @param $responses
     * @return ClassType[]
     */
    protected function extractClassesFromResponses($responses): array
    {
        /** @var ClassType[]  $classes */
        $classes = [];
        foreach ($responses as $responseName => $responseStructure) {
            $class = new ClassType($responseName);
            $classes[] = $class;

            $class->addComment($responseStructure['description']);
            $schema = $responseStructure['content']['application/json']['schema'];
            $properties = $schema['properties'];
            $construct = new Method('__construct');
            $construct->setPublic();
            $requiredParameters = [];
            foreach ($properties as $propertyName => $propertyStructure) {
                if (array_key_exists('$ref', $propertyStructure)) {
                    // TODO implement
                    continue;
                }
                $property = $class->addProperty($propertyName);
                ['type' => $type, 'nullable' => $nullable, 'description' => $description] = $propertyStructure;
                $property->setType($type);
                $property->addComment($description);
                $property->setNullable($nullable);
                if (!$nullable) {
                    $requiredParameters[] = $property;
                }
            }

            $this->addContractorWithRequiredArgument($class, $requiredParameters);
        };
        return $classes;
    }


    /**
     * TODO move to separate class
     */
    private function createDirectoryIfNotExist(string $directory):void
    {
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }
    }

    /**
     * @param ClassType $class
     * @param string $subdirectory
     * @param $outputDirectory
     */
    protected function generateClassFile(ClassType $class, string $subdirectory, $outputDirectory): void
    {
        $file = new PhpFile();
        $namespace = $file->addNamespace($this->namespaceForResponses);
        $namespace->add($class);

        $directory = sprintf('%s/%s', $outputDirectory, $subdirectory);
        $filepath = sprintf('%s/%s.php', $directory, $class->getName());
        $this->createDirectoryIfNotExist($directory);
        file_put_contents(
            $filepath,
            $this->printer->printFile($file)
        );
    }

}