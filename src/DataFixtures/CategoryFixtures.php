<?php

namespace App\DataFixtures;

use App\Entity\Categories;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $date = new DateTimeImmutable(date('Y-m-d H:i:s'));

        $category = new Categories();
        $category->setName('Categoria_Default_REF');
        $category->setCreatedAt($date);
        $manager->persist($category);
        $this->setReference('dummy_category_data', $category);

        // create 5 categorys
        for ($i = 1; $i <= 5; $i++) {
            $category = new Categories();
            $category->setName('Categoria_Default_'.$i);
            $category->setCreatedAt($date);
            $manager->persist($category);
        }

        $manager->flush();
    }
}
