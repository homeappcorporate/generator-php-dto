<?php

namespace Homeapp\OpenapiGenerator;

use Homeapp\OpenapiGenerator\Command\CreateDTO;
use Homeapp\OpenapiGenerator\Deffenition\ClassDefinitionData;
use Homeapp\OpenapiGenerator\Writer;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\Printer;
use Psr\Log\LoggerInterface;

class FileClassGenerator
{
    private Printer $printer;
    private LoggerInterface $logger;
    private string $outputDirectory;
    private Writer $writer;

    public function __construct(Printer $printer, LoggerInterface $logger, Writer $fileWriter, string $outputDirectory = 'out')
    {
        $this->printer = $printer;
        $this->logger = $logger;
        $this->outputDirectory = $outputDirectory;
        $this->writer = $fileWriter;
    }

    public function generateClassFile(ClassDefinitionData $definition): void
    {
        $class = $definition->class;
        $file = new PhpFile();
        $namespace = $file->addNamespace($definition->namespace);
        $namespace->add($class);

        $filepath = $this->getFilepathForClass($definition->namespace, $class);
        $this->logger->debug('Generating class {namespace}\{class} in {path}', [
            'namespace' => $definition->namespace,
            'class' => $class->getName(),
            'path' => $filepath,
        ]);
        $this->writer->write($filepath, $this->printer->printFile($file));
    }

    /**
     * @param string $namespaceName
     * @param ClassType $class
     * @return string
     */
    protected function getFilepathForClass(string $namespaceName, ClassType $class): string
    {
        $directory = sprintf('%s/%s', $this->outputDirectory, str_replace('\\', '/', $namespaceName));
        return sprintf('%s/%s.php', $directory, $class->getName());
    }

    public function setOutputDirectory(string $outputDirectory): void
    {
        $this->outputDirectory = $outputDirectory;
    }
}