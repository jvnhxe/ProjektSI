<?php
/**
 * This file is part of your project.
 *
 * (c) Your Name or Company <you@example.com>
 *
 * @license MIT
 */
namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

/**
 * Voter odpowiedzialny za decyzje dostępu do akcji edycji hasła użytkownika.
 *
 * Zezwala, gdy:
 *  - użytkownik ma rolę ROLE_ADMIN, lub
 *  - użytkownik edytuje **samego siebie**.
 */
class UserVoter extends Voter
{
    /** Nazwa atrybutu używanego w IsGranted/AuthorizationChecker. */
    public const EDIT_PASSWORD = 'USER_EDIT';

    /**
     * @param Security $security Serwis bezpieczeństwa do sprawdzenia ról.
     */
    public function __construct(private Security $security)
    {
    }

    /**
     * Sprawdza, czy ten voter obsługuje dany atrybut i subject.
     *
     * @param string     $attribute Nazwa atrybutu (np. USER_EDIT)
     * @param mixed      $subject   Oczekiwany: App\Entity\User
     *
     * @return bool True, jeżeli voter powinien rozpatrywać żądanie
     */
    protected function supports(string $attribute, $subject): bool
    {
        return $attribute === self::EDIT_PASSWORD && $subject instanceof User;
    }

    /**
     * Właściwa decyzja o przyznaniu dostępu.
     *
     * @param string         $attribute Nazwa atrybutu
     * @param mixed          $subject   Oczekiwany: App\Entity\User (użytkownik, którego dotyczy operacja)
     * @param TokenInterface $token     Token aktualnie zalogowanego użytkownika
     *
     * @return bool True, gdy dostęp dozwolony
     */
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
