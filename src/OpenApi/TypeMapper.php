<?php

namespace Homeapp\OpenapiGenerator\OpenApi;

use Nette\PhpGenerator\Type;
use Psr\Log\LoggerInterface;

final class TypeMapper
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function map(string $type):string
    {
        if ($type === 'boolean') {
            return Type::BOOL;
        }
        $this->logger->error('Unsupported type "{type}" use "mixed"', ['type' => $type]);
        return Type::MIXED;
    }
}