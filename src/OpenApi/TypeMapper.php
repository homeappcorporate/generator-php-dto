<?php

declare(strict_types=1);

namespace Homeapp\OpenapiGenerator\OpenApi;

use Nette\PhpGenerator\Type;
use Psr\Log\LoggerInterface;

final class TypeMapper
{
    private const TYPE_MAP = [
        'boolean' =>Type::BOOL,
        'integer' => Type::INT,
        'string' => Type::STRING,
    ];


    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function map(string $type): ?string
    {
        $convertedType = self::TYPE_MAP[$type] ?? null;
        if ($convertedType !== null) {
            return $convertedType;
        }
        $this->logger->error('Unsupported type "{type}" use "mixed"', ['type' => $type]);

        return null;
    }
}
