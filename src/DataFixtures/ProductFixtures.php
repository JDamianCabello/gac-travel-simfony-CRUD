<?php

namespace App\DataFixtures;

use App\Entity\Products;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $date = new DateTimeImmutable(date('Y-m-d H:i:s'));

        //Create 2 products
        for ($i = 1; $i <= 2; $i++) {
            $product = new Products();
            $product->setName('Producto_Default_'.$i);
            $product->setCategoryId($this->getReference('dummy_category_data'));
            $product->setCreatedAt($date);
            $product->setStock(rand(0,1000));
            $manager->persist($product);
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
