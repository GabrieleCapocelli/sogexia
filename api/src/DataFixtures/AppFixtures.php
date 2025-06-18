<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Product;
use App\Enum\Status;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $product = new Product();
        $product->setName('Test Product');
        $product->setDescription('A demo product for testing');
        $product->setPrice(99.99);
        $product->setStatus(Status::Available);
        $product->setStockSold(10);
        $product->setStockAvailable(90);

        $manager->persist($product);
        $manager->flush();
    }
}
