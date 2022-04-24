<?php

declare(strict_types=1);

namespace App\Security\UserChecker;

use App\Entity\User;
use App\Security\Exception\AccountSuspendedException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{

    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            //@codeCoverageIgnore
            return;
        }

        if ($user->isSuspended() === true) {
            throw new AccountSuspendedException(
                "Your account has been suspended. Please contact the administrator."
            );
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        // TODO: Implement checkPostAuth() method.
    }
}