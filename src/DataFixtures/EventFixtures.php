<?php

namespace App\DataFixtures;

use App\Entity\Campus;
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



    private $encoder;


    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    //Création de fake datas sur toutes les tables
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');

        $campuses = [];
        for ($i = 0; $i < 5; $i++)  {

            $campus = new Campus();
            $campus->setName($faker->monthName);
            $campuses[] = $campus;
            $manager->persist($campus);
            $manager->flush();
        }


        $statuses = ['Creating', 'Opened', 'Closed', 'Running', 'Over', 'Cancelled', 'Archived'];
        $statusObjects = [];
        for ($i=0; $i < sizeof($statuses); $i++)  {
            $status = new Status();
            $status -> setLabel($statuses[$i]);
            $manager->persist($status);
            $statusObjects [] = $status;
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

        $users = [];
        for ($i = 0; $i < 20; $i++)
        {
            $user = new User();
            $password = $this->encoder->encodePassword($user, '1234');

            $user->setUsername($faker->userName);
            $user->setFirstname($faker->firstName);
            $user->setLastname($faker->lastName);
            $user->setAdmin(true);
            $user->setActive(true);
            $user->setMail($faker->email);
            $user->setPassword($password);
            $user->setCampus($faker->randomElement($campuses));
            $users[] = $user;
            $manager->persist($user);
            $manager->flush();
        }

        for ($i = 0; $i < 20; $i++)
        {
            $rand = rand(1, 7);
            $event = new Event();
            $event->setClosingDate($faker->dateTimeBetween('2020-09-18 08:37:17','2020-12-31 08:37:17','UTC'));
            $event->setName($faker->word);
            $event->setDescription($faker->text);
            $event->setPlace($faker->randomElement($places));
            $startDate = clone  $event->getClosingDate();
            $startDate -> add(new \DateInterval('P2D'));
            $event->setStartDate($startDate);
            $event->setDuration('5');
            $event->setStatus($faker->randomElement($statusObjects));
            $event->setMaxAttendees(rand(7, 15));
            $event->setUrlPicture(null);
            $event->setOrganizer($faker->randomElement($users));
            $event->setCampus($faker->randomElement($campuses));

            for ($j= 0; $j < $rand; $j++) {
            $event->addAttendee($faker->randomElement($users));
            }
            $manager->persist($event);
            $manager->flush();

        }
    }
}
