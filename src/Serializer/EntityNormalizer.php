<?php

namespace App\Serializer;

use App\Helpers\Utils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class EntityNormalizer implements NormalizerInterface
{
    /**
     * @var ObjectNormalizer
     */
    private $normalizer;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    private const BLACKLIST = [
        '__isInitialized__',
        '__initializer__',
        '__cloner__'
    ];

    public function __construct(ObjectNormalizer $normalizer, EntityManagerInterface $entityManager)
    {
        $this->normalizer = $normalizer;
        $this->entityManager = $entityManager;
    }

    public function normalize($topic, $format = null, array $context = [])
    {
        $data = $this->normalizer->normalize($topic, $format, $context);

        return $this->filter($data);
    }

    public function supportsNormalization($data, $format = null, array $context = [])
    {
        return Utils::isDoctrineEntity($this->entityManager, $data);
    }

    private function filter(array $data): array
    {
        foreach($data as $key => $value) {
            if(is_array($value)) {
                $data[$key] = $this->filter($value);
            }
        }

        $data = array_filter($data, function ($key) {
            return !in_array($key, self::BLACKLIST);
        }, ARRAY_FILTER_USE_KEY);

        return $data;
    }
}