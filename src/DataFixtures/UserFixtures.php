<?php

namespace App\DataFixtures;

use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $adminUsername = 'admin';
        $adminPassword = 'admin';
        $date = new DateTimeImmutable(date('Y-m-d H:i:s'));
        // create admin user
        $adminUser = new User();
        $adminUser->setUsername($adminUsername);
        $adminUser->setActive(true);
        $adminUser->setCreatedAt($date);
        $adminUser->setRoles(['ROLE_USER']);

        $factory = new PasswordHasherFactory([
            'common' => ['algorithm' => 'auto'],
            'memory-hard' => ['algorithm' => 'auto'],
        ]);

        $passwordHasher = $factory->getPasswordHasher('common');
        $hash = $passwordHasher->hash($adminPassword);

        $adminUser->setPassword($hash);

        
        $manager->persist($adminUser);
        $manager->flush();
    }
}
