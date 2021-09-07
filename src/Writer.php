<?php

declare(strict_types=1);

namespace Homeapp\OpenapiGenerator;

class Writer
{
    public function write(string $path, string $content): void
    {
        $this->createDirectoryIfNotExist(dirname($path));

        file_put_contents(
            $path,
            $content
        );
    }

    private function createDirectoryIfNotExist(string $directory): void
    {
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }
    }
}
