<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Post;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class PostFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $categories = $manager->getRepository(Category::class)->findAll();
        $categoriesCount = count($categories) - 1;

        for ($i = 0; $i < 20; $i++) {
            $post = new Post();
            $post->setName('post '.$i);
            $post->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum');

            $category = !empty($categories) ? $categories[rand(0, $categoriesCount)] : null;

            $post->setCategory($category);

            $manager->persist($post);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CategoryFixtures::class,
        ];
    }
}
