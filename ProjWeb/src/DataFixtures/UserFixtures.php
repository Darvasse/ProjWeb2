<?php

namespace App\DataFixtures;

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
        $user = new Users();
        $user->setEmail('admin@admin.fr');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->passwordEncoder->hashPassword(
          $user,'admin'
        ));
        $manager->persist($user);

        $manager->flush();
    }
}
