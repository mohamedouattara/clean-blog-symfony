<?php

declare(strict_types=1);

namespace App\EntityListener;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserListener
{

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function prePersist(User $user): void
    {
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $user->getPlainPassword())
        );
    }

}