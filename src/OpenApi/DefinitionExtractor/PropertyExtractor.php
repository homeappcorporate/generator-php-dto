<?php

declare(strict_types=1);

namespace Homeapp\OpenapiGenerator\OpenApi\DefinitionExtractor;

use Homeapp\OpenapiGenerator\AnnotationHelper;
use Homeapp\OpenapiGenerator\OpenApi\ComponentsExtractors\PropertyExtractorOptions;
use Homeapp\OpenapiGenerator\OpenApi\RefFullClassNameConverter;
use Homeapp\OpenapiGenerator\OpenApi\TypeMapper;
use Nette\PhpGenerator\Property;
use Nette\PhpGenerator\Type;
use Psr\Log\LoggerInterface;

class PropertyExtractor
{
    private RefFullClassNameConverter $refFullClassNameConverter;
    private TypeMapper $typeMapper;
    private LoggerInterface $logger;
    private AnnotationHelper $annotationHelper;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(RefFullClassNameConverter $refFullClassNameConverter, TypeMapper $typeMapper, LoggerInterface $logger, AnnotationHelper $annotationHelper)
    {
        $this->refFullClassNameConverter = $refFullClassNameConverter;
        $this->typeMapper = $typeMapper;
        $this->logger = $logger;
        $this->annotationHelper = $annotationHelper;
    }

    public function extractProperty(string $propertyName, array $propertyStructure, PropertyExtractorOptions $options = null):?Property
    {
        $this->logger->debug('Trying to extract property "{propertyName}"', [
            'propertyName' => $propertyName,
        ]);
        if ($options === null) {
            $options = new PropertyExtractorOptions(true, true);
        }
        /**
         * @var null|bool $nullable
         * @var string $type
         * @var null|string $description
         */
        ['nullable' => $nullable, 'description' => $description, '$ref' => $ref, 'type' => $type, 'default' => $default, 'readOnly' => $readOnly, 'writeOnly' => $writeOnly, 'items' => $items] = $propertyStructure;

        if ($readOnly === true && $options->addReadOnly === false) {
            return null;
        }
        if ($writeOnly === true && $options->addWriteOnly === false) {
            return null;
        }
        $property = new Property($propertyName);

        $nullable = $nullable ?? false;
        if ($description !== null) {
            $property->addComment($description);
        }
        if (is_string($ref)) {
            $refClassName = $this->refFullClassNameConverter->convertRefToFullClassName($ref);
            $property->setType($refClassName);
            $property->setNullable(false);
            return $property;
        }
        $this->logger->debug('"{propertyName}" has type "{type}"', [
            'propertyName' => $propertyName,
            'type' => $type,
        ]);
        if ($type === 'array') {
            $property->setType(Type::ARRAY);
            ['$ref' => $ref] = $items;
            $itemRefClass = $this->refFullClassNameConverter->convertRefToFullClassName($ref);
            $this->logger->debug('items of "{itemClass}"', [
                'itemClass' => $itemRefClass,
            ]);
            $property->addComment($this->annotationHelper->returnArrayOfType($itemRefClass));

        } else {
            $property->setType($this->typeMapper->map($type));
        }

        $property->setNullable($nullable);
        if ($default === 'null') {
            $property->setValue(null);
        }
        return $property;
    }
}
