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
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class EntityDenormalizer extends ObjectNormalizer implements DenormalizerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, ClassMetadataFactoryInterface $classMetadataFactory = null, NameConverterInterface $nameConverter = null, PropertyAccessorInterface $propertyAccessor = null, PropertyTypeExtractorInterface $propertyTypeExtractor = null, ClassDiscriminatorResolverInterface $classDiscriminatorResolver = null, callable $objectClassResolver = null, array $defaultContext = [])
    {
        parent::__construct($classMetadataFactory, $nameConverter, $propertyAccessor, $propertyTypeExtractor, $classDiscriminatorResolver, $objectClassResolver, $defaultContext);

        $this->entityManager = $entityManager;
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
}