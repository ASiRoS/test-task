<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CategoryController
 * @package App\Controller
 * @Route("/api/category")
 */
class CategoryController extends ApiController
{
    protected static function getEntity()
    {
        return Category::class;
    }
}