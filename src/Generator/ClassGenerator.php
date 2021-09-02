<?php

namespace Homeapp\OpenapiGenerator\Generator;

use Homeapp\OpenapiGenerator\Command\CreateDTO;
use Homeapp\OpenapiGenerator\Writer;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\Printer;
use Psr\Log\LoggerInterface;

class ClassGenerator
{
    private Printer $printer;
    private LoggerInterface $logger;
    private string $outputDirectory;
    private Writer $writer;

    public function __construct(Printer $printer, LoggerInterface $logger, Writer $fileWriter, string $outputDirectory)
    {
        $this->printer = $printer;
        $this->logger = $logger;
        $this->outputDirectory = $outputDirectory;
        $this->writer = $fileWriter;
    }

    /**
     * @param ClassType $class
     * @param string $namespace
     * @param $namespace
     */
    public function generateClassFile(ClassType $class, string $namespaceName): void
    {
        $file = new PhpFile();
        $namespace = $file->addNamespace($namespaceName);
        $namespace->add($class);

        $filepath = $this->getFilepathForClass($namespaceName, $class);
        $this->logger->debug('Generating class {namespace}\{class} in {path}', [
            'namespace' => $namespaceName,
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
        $filepath = sprintf('%s/%s.php', $directory, $class->getName());
        return $filepath;
    }
}