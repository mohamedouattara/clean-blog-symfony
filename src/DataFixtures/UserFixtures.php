<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $userAdmin = (new User())
            ->setEmail('admin@email.com')
            ->setPlainPassword('password')
            ->setNickname('admin')
            ->setRoles(['ROLE_ADMIN']);

        $user = (new User())
            ->setEmail('email@email.com')
            ->setPlainPassword('password')
            ->setNickname('Roger');

        $userSuspended = (new User())
            ->setEmail('suspended@email.com')
            ->setPlainPassword('password')
            ->setNickname('user.suspended')
            ->setSuspendedAt(new DateTimeImmutable());

        $manager->persist($userSuspended);
        $manager->persist($userAdmin);
        $manager->persist($user);

        $manager->flush();
    }
}
