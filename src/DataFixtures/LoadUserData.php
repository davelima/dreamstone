<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Administrator;

class LoadUserData extends Fixture implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $administrator = new Administrator();
        $administrator->setName('Administrator');
        $administrator->setUsername('dreamstone');
        $administrator->setPassword(password_hash('dreamstone', \PASSWORD_DEFAULT));
        $administrator->setEmail('dreamstone@dreamstone.com');
        $administrator->setRoles(['ROLE_SUPER_ADMIN']);

        $manager->persist($administrator);
        $manager->flush();
    }
}
