<?php
namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class UserVoter extends Voter
{
    public const EDIT_PASSWORD = 'USER_EDIT';

    public function __construct(private Security $security) {}

    protected function supports(string $attribute, $subject): bool
    {
        return $attribute === self::EDIT_PASSWORD && $subject instanceof User;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var User|null $logged */
        $logged = $token->getUser();
        if (!$logged instanceof User) {
            return false; // niezalogowany
        }

        // admin?
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // właściciel konta?
        return $subject === $logged;
    }
}
