<?php

namespace App\DataFixtures;

use App\Entity\City;
use App\Entity\Event;
use App\Entity\Place;
use App\Entity\Status;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class EventFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');


        $statuses = [];

        for ($i = 0; $i < 5; $i++)  {

            $status = new Status();
            $status->setLabel($faker->colorName);
            $statuses[] = $status;
            $manager->persist($status);
            $manager->flush();
        }

        $cities = [];
        for ($i = 0; $i < 10; $i++) {

            $city = new City();
            $city->setName($faker->city);
            $city->setPostCode(00000);
            $cities[] = $city;
            $manager->persist($city);
            $manager->flush();
        }

        $places = [];
        for ($i = 0; $i < 10; $i++) {

            $place = new Place();
            $place->setName($faker->company);
            $place->setCity($faker->randomElement($cities));
            $place->setLatitude(113.492);
            $place->setLongitude(929.232);
            $places[] = $place;
            $manager->persist($place);
            $manager->flush();
        }

        for ($i = 0; $i < 20; $i++)

        {
            $event = new Event();
            $event->setStartDate($faker->dateTime('2008-04-25 08:37:17', 'UTC'));
            $event->setName($faker->word);
            $event->setDescription($faker->text);
            $event->setPlace($faker->randomElement($places));
            $event->setClosingDate($faker->dateTime('2008-04-25 08:37:17', 'UTC'));
            $event->setDuration('5');
            $event->setStatus($faker->randomElement($statuses));
            $event->setUrlPicture(null);

            $manager->persist($event);
            $manager->flush();

        }


    }
}
