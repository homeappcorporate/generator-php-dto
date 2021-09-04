<?php

declare(strict_types=1);

use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

$builder = new DI\ContainerBuilder();
$builder->useAutowiring(true);
$builder->addDefinitions(
    [
        LoggerInterface::class => DI\get(Logger::class),
        Logger::class => DI\create()
            ->constructor('app', [
                DI\get('monolog.handler.debug'),
            ], [
                  DI\get(PsrLogMessageProcessor::class),
              ]),
        'monolog.handler.debug' => DI\create('Monolog\Handler\StreamHandler')
            ->constructor('php://stdout'),
        'monolog.handler.error' => DI\create('Monolog\Handler\StreamHandler')
            ->constructor('php://stderr', LogLevel::ERROR),
    ]
);

return $builder->build();
