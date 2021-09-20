<?php

namespace Homeapp\OpenapiGenerator;

class AnnotationHelper
{
    public function returnArrayOfType(string $type):string
    {
        return sprintf('@return %s[]', $type);
    }
}