<?php

namespace Homeapp\OpenapiGenerator;

use Psr\Log\LoggerInterface;

class ClassDefinitionRegister
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    private array $map = [];

    public function register(string $xpath, object $class):void
    {
        if (array_key_exists($xpath, $this->map)){
            $this->logger->debug('Class by "path" already registered', [
                'path' => $xpath,
                'class' => get_class($class),
                'registered' => $this->map[$xpath],
            ]);
            return;
        }
        $this->map[$xpath] = $class;
    }
}