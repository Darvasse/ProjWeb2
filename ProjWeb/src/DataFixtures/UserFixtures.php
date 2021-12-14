<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Users;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function  __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager): void
    {
        $c1 = new Category();
        $c1->setName("Action");

        $c2 = new Category();
        $c2->setName("Adventure");

        $c3 = new Category();
        $c3->setName("Role-Playing");

        $c4 = new Category();
        $c4->setName("Simulation");

        $c5 = new Category();
        $c5->setName("Strategy");

        $c6 = new Category();
        $c6->setName("Sport & Racing");

        $manager->persist($c1);
        $manager->persist($c2);
        $manager->persist($c3);
        $manager->persist($c4);
        $manager->persist($c5);
        $manager->persist($c6);

        $manager->flush();
    }
}
