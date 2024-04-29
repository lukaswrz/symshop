<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class EntityJsonSerializerService
{
    private readonly Serializer $serializer;

    public function __construct()
    {
        $this->serializer = new Serializer(
            [new ObjectNormalizer(new ClassMetadataFactory(new AttributeLoader()))],
            [new JsonEncoder()]
        );
    }

    public function serialize(mixed $entity): string
    {
        return $this->serializer->serialize($entity, 'json');
    }
}
