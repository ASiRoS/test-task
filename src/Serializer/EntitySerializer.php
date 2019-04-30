<?php

namespace App\Serializer;

use App\Helpers\Utils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Mapping\ClassDiscriminatorResolverInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class EntitySerializer extends ObjectNormalizer implements NormalizerInterface, DenormalizerInterface
{
    private const BLACKLIST = [
        '__isInitialized__',
        '__initializer__',
        '__cloner__'
    ];

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ObjectNormalizer
     */
    private $normalizer;

    public function __construct(ObjectNormalizer $normalizer, EntityManagerInterface $entityManager, ClassMetadataFactoryInterface $classMetadataFactory = null, NameConverterInterface $nameConverter = null, PropertyAccessorInterface $propertyAccessor = null, PropertyTypeExtractorInterface $propertyTypeExtractor = null, ClassDiscriminatorResolverInterface $classDiscriminatorResolver = null, callable $objectClassResolver = null, array $defaultContext = [])
    {
        parent::__construct($classMetadataFactory, $nameConverter, $propertyAccessor, $propertyTypeExtractor, $classDiscriminatorResolver, $objectClassResolver, $defaultContext);

        $this->entityManager = $entityManager;
        $this->normalizer = $normalizer;
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        $object = new $class;

        foreach($data as $field => $value) {
            if(is_array($value)) {
                $value = $this->entityManager->getRepository($this->getEntityRelativePath($field))->findOneBy($value);
            }

            $this->setAttributeValue($object, $field, $value);
        }

        return $object;
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

    public function supportsDenormalization($data, $type, $format = null)
    {
        return Utils::isDoctrineEntity($this->entityManager, $type);
    }

    private function getEntityRelativePath($entityName): string
    {
        if(is_array($entityName)) {
            $entityName = array_key_first($entityName);
        } elseif(!is_string($entityName)) {
            throw new \InvalidArgumentException('Data must be array or string');
        }

        return 'App\Entity\\' . ucfirst($entityName);
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