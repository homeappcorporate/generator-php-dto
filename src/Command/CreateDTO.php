<?php
declare(strict_types=1);

namespace Homeapp\OpenapiGenerator\Command;

use Homeapp\OpenapiGenerator\OpenApi\PHPClassDefinitionExtractor;
use Homeapp\OpenapiGenerator\DTO\ClassDefinitionData;
use Homeapp\OpenapiGenerator\Generator\FileClassFactoryGenerator;
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
    private FileClassFactoryGenerator $classFactoryGenerator;
    private PHPClassDefinitionExtractor $phpClassDefinitionExtractor;

    public function __construct(
        string $name = null,
        Printer $printer,
        LoggerInterface $logger,
        TypeMapper $typeMapper,
        PHPClassDefinitionExtractor $phpClassDefinitionExtractor,
        FileClassFactoryGenerator $classFactoryGenerator
    ) {
        parent::__construct($name);
        $this->printer = $printer;
        $this->logger = $logger;
        $this->typeMapper = $typeMapper;
        $this->classFactoryGenerator = $classFactoryGenerator;
        $this->phpClassDefinitionExtractor = $phpClassDefinitionExtractor;
    }

    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'create-dto';

    /**
     * @return TypeMapper
     */
    public function getTypeMapper(): TypeMapper
    {
        return $this->typeMapper;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Generate class from json')
            ->addArgument('path-json', InputArgument::REQUIRED, 'From which class will be generated')
            ->addArgument(
                'path-out',
                InputArgument::OPTIONAL,
                'Where put generated content',
                sprintf('%s/out', getcwd())
            )
            ->addOption(
                'namespace',
                null,
                InputOption::VALUE_OPTIONAL,
                'Namespace what all generated classes will have',
                'Generated\\'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = $input->getArgument('path-json');
        $json = json_decode(file_get_contents($path), true);
        $outputDirectory = $input->getArgument('path-out');
        $classGenerator = $this->classFactoryGenerator->build($outputDirectory);
        $globalNamespace = $input->getOption('namespace');

        $responses = $json['components']['responses'];
        $classesFromResponses = $this->extractClassesFromResponses($responses);
        foreach ($classesFromResponses as $classData) {
            $classData->class->setFinal();
            $classGenerator->generateClassFile($classData->class, sprintf('%s%s', $globalNamespace, $classData->namespace));
        }
        return Command::SUCCESS;
    }


    /**
     * @param $responses
     * @return ClassDefinitionData[]
     */
    private function extractClassesFromResponses($responses): array
    {
        $result = [];
        foreach ($responses as $responseName => $responseStructure) {
            $schema = $responseStructure['content']['application/json']['schema'];
            $classes = $this->phpClassDefinitionExtractor->extractClassesDefinition(
                $responseName,
                'Responses',
                $responseStructure['description'],
                $schema['properties'],
            );
            array_push($result, ...$classes);
        }
        return $result;
    }
}