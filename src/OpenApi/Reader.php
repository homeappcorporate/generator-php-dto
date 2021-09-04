<?php

declare(strict_types=1);

namespace Homeapp\OpenapiGenerator\OpenApi;

use RuntimeException;
use JsonException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class Reader
{
    /**
     *
     * @throws JsonException|ParseException|RuntimeException
     */
    public function extractPhpArrayFromFile(string $path): array
    {
        $realpath = realpath($path);
        $ext  = pathinfo($path, PATHINFO_EXTENSION);
        if ($ext === 'json') {
            /** @var array $openapi  */
            $openapi = json_decode(file_get_contents($realpath), true, 512, JSON_THROW_ON_ERROR);
        } elseif (in_array($ext, ['yaml', 'yml'])) {
            /** @var array $openapi  */
            $openapi = Yaml::parseFile($realpath);
        } else {
            throw new RuntimeException(sprintf('Extension "%s" is not supported', $ext));
        }
        return $openapi;
    }
}
