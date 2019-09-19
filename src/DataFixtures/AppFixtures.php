<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

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
                }
            }
        }

        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
