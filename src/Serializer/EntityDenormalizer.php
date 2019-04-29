<?php

namespace App\Serializer;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class EntityDenormalizer implements DenormalizerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        $object = new $class;

        foreach($data as $field => $value) {
            $methodName = 'set' . ucfirst($field);
            $setter = [$object, $methodName];

            if(method_exists($object, $methodName)) {
                if(is_array($value)) {
                    $entityName = 'App\Entity\\' . ucfirst($field);
                    $repository = $this->entityManager->getRepository($entityName);

                    $value = $repository->findOneBy($value);
                }

                call_user_func($setter, $value);
            }

        }

        return $object;
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return is_object($data);
    }
}