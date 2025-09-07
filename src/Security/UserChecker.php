<?php
namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }
        if ($user->isBlocked()) {
            throw new CustomUserMessageAccountStatusException('Twoje konto zostało zablokowane przez administratora.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        // nic
    }
}
