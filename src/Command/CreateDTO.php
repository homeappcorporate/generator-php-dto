<?php declare(strict_types=1);

namespace Homeapp\OpenapiGenerator\Command;

use Homeapp\OpenapiGenerator\Generator\ClassFactoryGenerator;
use Homeapp\OpenapiGenerator\Generator\ClassGenerator;
use Homeapp\OpenapiGenerator\OpenApi\TypeMapper;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\Parameter;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\Printer;
use Nette\PhpGenerator\Type;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class CreateDTO extends Command
{
    private TypeMapper $typeMapper;
    private ClassFactoryGenerator $classFactoryGenerator;

    public function __construct(string $name = null, Printer $printer, LoggerInterface $logger, TypeMapper $typeMapper, ClassFactoryGenerator $classFactoryGenerator)
    {
        parent::__construct($name);
        $this->printer = $printer;
        $this->logger = $logger;
        $this->typeMapper = $typeMapper;
        $this->classFactoryGenerator = $classFactoryGenerator;
    }

    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'create-dto';

    protected function configure(): void
    {
        $this
            ->setDescription('Generate class from json')
            ->addArgument('path-json', InputArgument::REQUIRED, 'From which class will be generated')
            ->addArgument('path-out', InputArgument::OPTIONAL, 'Where put generated content', sprintf('%s/out', getcwd()))
            ->addOption('namespace', null, InputOption::VALUE_OPTIONAL, 'Namespace what all generated classes will have', 'Generated\\');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path =  $input->getArgument('path-json');
        $json = json_decode(file_get_contents($path), true);
        $outputDirectory = $input->getArgument('path-out');
        $classGenerator = $this->classFactoryGenerator->build($outputDirectory);
        $globalNamespace = $input->getOption('namespace');

        $responses = $json['components']['responses'];
        $classesFromResponses = $this->extractClassesFromResponses($responses);
        $subNamespace = 'Responses';
        foreach ($classesFromResponses as $class) {
            $class->setFinal();
            $classGenerator->generateClassFile($class, sprintf('%s%s', $globalNamespace, $subNamespace));
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
                $property->setType($this->typeMapper->map($type));
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
}