<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $hasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setRoles(['ROLE_ADMIN'])
            ->setEmail('admin@doe.fr')
            ->setUsername('admin')
            ->setIsVerified(true)
            ->setApiToken('admin_token')
            ->setPassword($this->hasher->hashPassword($user, 'admin'));
        $this->addReference('ADMIN_USER', $user);
        $manager->persist($user);

        for ($i = 1; $i <= 10; $i++) {
            $user = new User();
            $user->setRoles([])
                ->setEmail("user{$i}@doe.fr")
                ->setUsername("user{$i}")
                ->setIsVerified(true)
                ->setApiToken("user{$i}")
                ->setPassword($this->hasher->hashPassword($user, '0000'));
            $this->addReference('USER' . $i, $user);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
