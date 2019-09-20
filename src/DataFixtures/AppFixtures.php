<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Command;
use App\Entity\Product;
use App\Entity\Category;
use App\Entity\CommandProduct;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new \DavidBadura\FakerMarkdownGenerator\FakerProvider($faker));
        $faker->addProvider(new \Liior\Faker\Prices($faker));

        $categoryTitles = [
            "High-tech" => ["Ordinateurs", "Matériel", "Audio"],
            "Geekeries" => ["Habits", "Goodies"],
        ];

        $products = [];

        foreach ($categoryTitles as $title => $subTitles) {
            $category = new Category;
            $category->setTitle($title);
            $manager->persist($category);

            foreach ($subTitles as $subTitle) {
                $subCategory = new Category();
                $subCategory->setTitle($subTitle)
                    ->setParent($category);
                $manager->persist($subCategory);

                for ($p = 0; $p < mt_rand(5, 15); $p++) {
                    $product = new Product();
                    $product->setTitle($faker->catchPhrase(40))
                        ->setIntroduction($faker->markdownP())
                        ->setDescription(
                            $faker->markdownP() . "\n\n" . $faker->markdownH3() . "\n\n" . $faker->markdownP() . "\n\n" . $faker->markdownP()
                        )
                        ->setPrice($faker->price(20, 500))
                        ->setPicture($faker->imageUrl(210,110)) 
                        // 20% de chance de tirer un true
                        ->setFeatured($faker->boolean(20))
                        ->setCategorie($subCategory);

                    $manager->persist($product);

                    $products[] = $product;
                }
            }
        }


        for ($c = 0; $c < 10; $c++) {
            $command = new Command();
            $command->setAddress($faker->address)
                ->setCreatedAt($faker->dateTimeBetween("-6 months"));

            $manager->persist($command);

            // Faire les liens avec les produits
            $randomProducts = $faker->randomElements($products, 4);

            foreach ($randomProducts as $product) {
                $commandProduct = new CommandProduct();
                $commandProduct->setProduct($product)
                    ->setCommand($command)
                    ->setQuantity(mt_rand(1, 3));

                //$command->addCommandProduct($commandProduct);

                $manager->persist($commandProduct);
            }

        }


        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
