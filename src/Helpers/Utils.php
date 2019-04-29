<?php

namespace App\Helpers;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Proxy\Proxy;

class Utils
{
    /**
     * @param EntityManager $em
     * @param string|object $class
     * @return bool
     */
    public static function isDoctrineEntity(EntityManager $em, $class)
    {
        if(is_object($class)) {
            $class = ($class instanceof Proxy) ? get_parent_class($class) : get_class($class);
        }

        if(!is_string($class)) {
            return false;
        }

        return !$em->getMetadataFactory()->isTransient($class);
    }
}