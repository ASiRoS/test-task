<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PostController
 * @package App\Controller
 * @Route("/api/post")
 */
class PostController extends ApiController
{
    protected static function getEntity()
    {
        return Post::class;
    }
}