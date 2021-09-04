<?php

declare(strict_types=1);

namespace Homeapp\OpenapiGenerator;

class ArrayPathFetcher
{
    public function getContent(string $xpath, array &$array): array
    {
        $path = explode('.', $xpath);
        $temp =& $array;

        return $this->getTemp($path, $temp);
    }

    public function getPrevious(string $xpath, array &$array): array
    {
        $path = explode('.', $xpath);
        array_pop($path);
        $temp =& $array;

        return $this->getTemp($path, $temp);
    }

    /**
     * @param $path
     * @param $temp
     * @return mixed
     */
    protected function getTemp($path, &$temp)
    {
        foreach ($path as $key) {
            $temp =& $temp[$key];
        }
        return $temp;
    }
}
