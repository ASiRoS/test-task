<?php

namespace App\Serializer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class EntityNormalizer implements NormalizerInterface
{
    /**
     * @var ObjectNormalizer
     */
    private $normalizer;

    private const BLACKLIST = [
        '__isInitialized__',
        '__initializer__',
        '__cloner__'
    ];

    public function __construct(ObjectNormalizer $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    public function normalize($topic, $format = null, array $context = [])
    {
        $data = $this->normalizer->normalize($topic, $format, $context);

        $data = array_filter($data, function ($key) {
            return !in_array($key, self::BLACKLIST);
        }, ARRAY_FILTER_USE_KEY);

        return $data;
    }

    public function supportsNormalization($data, $format = null, array $context = [])
    {
        return is_object($data);
    }
}