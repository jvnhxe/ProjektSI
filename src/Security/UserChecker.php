<?php
/*
 * This file is part of the YourProject package.
 *
 * (c) Your Name <your-email@example.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * UserChecker validates user account state during authentication.
 *
 * Currently denies authentication if the account is blocked.
 */
class UserChecker implements UserCheckerInterface
{
    /**
     * Pre-authentication checks executed before credentials are validated.
     *
     * If the provided user is an instance of {@see User} and is marked as blocked,
     * authentication will be denied with a user-friendly message.
     *
     * @param UserInterface $user The user being authenticated.
     *
     * @return void
     *
     * @throws CustomUserMessageAccountStatusException When the account is blocked.
     */
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        if ($user->isBlocked()) {
            throw new CustomUserMessageAccountStatusException('Twoje konto zostało zablokowane przez administratora.');
        }
    }

    /**
     * Post-authentication checks executed after credentials are validated.
     *
     * Placeholder for additional checks; currently no-op.
     *
     * @param UserInterface $user The authenticated user.
     *
     * @return void
     */
    public function checkPostAuth(UserInterface $user): void
    {
    }
}
