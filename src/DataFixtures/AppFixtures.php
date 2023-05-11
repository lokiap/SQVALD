<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\CategoryNews;
use App\Entity\Document;
use App\Entity\Event;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;


class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        /*
         * Création de fausses données pour l'utilisateurs
         */
        $faker = Faker\Factory::create('fr_FR');

		$events = [];
		for ($i = 0; $i < 10; $i++) {
			$events[$i] = new Event();
			$events[$i]->setTitle($faker->title());
			$events[$i]->setCategory($manager->getRepository(CategoryNews::class)->findAll()[$faker->numberBetween(0, 3)]);
			$events[$i]->setIsActive(true);
			$events[$i]->setResume($faker->sentence(15));
			$dateStart = $faker->dateTimeBetween('now', '+5 years');
			$events[$i]->setDateBegin($dateStart);
			$manager->persist($events[$i]);
		}

        $manager->flush();
    }
}
