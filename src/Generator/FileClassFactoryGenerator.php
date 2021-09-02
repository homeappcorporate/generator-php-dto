<?php

namespace Homeapp\OpenapiGenerator\Generator;

use Homeapp\OpenapiGenerator\Writer;
use Nette\PhpGenerator\Printer;
use Psr\Log\LoggerInterface;

class FileClassFactoryGenerator
{
    private Printer $printer;
    private LoggerInterface $logger;
    private Writer $writer;

    public function __construct(Printer $printer, LoggerInterface $logger, Writer $writer)
    {
        $this->printer = $printer;
        $this->logger = $logger;
        $this->writer = $writer;
    }

    public function build(string $outputDirectory):ClassGenerator
    {
        return new ClassGenerator($this->printer, $this->logger, $this->writer, $outputDirectory);
    }

}