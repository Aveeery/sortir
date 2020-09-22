<?php

namespace App\DataFixtures;

use App\Entity\Event;
use App\Entity\Status;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');

        for ($i = 0; $i < 20; $i++)
        {
            $user = new User();
            $password = $this->encoder->encodePassword($user, '1234');

            $user->setUsername($faker->userName);
            $user->setFirstname($faker->firstName);
            $user->setLastname($faker->lastName);
            $user->setAdmin(false);
            $user->setActive(true);
            $user->setMail($faker->email);
            $user->setPassword($password);
            $manager->persist($user);

        }

        $manager->flush();
    }
}
