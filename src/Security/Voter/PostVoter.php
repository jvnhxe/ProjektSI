<?php
/**
 * Post voter.
 *
 * Decyduje, czy użytkownik może oglądać/edytować/usuwać post.
 */

namespace App\Security\Voter;

use App\Entity\Post;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class PostVoter.
 *
 * Atrybuty: VIEW, EDIT, DELETE.
 */
final class PostVoter extends Voter
{
    public const VIEW   = 'VIEW';
    public const EDIT   = 'EDIT';
    public const DELETE = 'DELETE';

    /**
     * Sprawdza, czy voter obsługuje dany atrybut i obiekt.
     *
     * @param string      $attribute Atrybut (VIEW/EDIT/DELETE)
     * @param mixed|null  $subject   Przedmiot głosowania (Post)
     *
     * @return bool Czy wspieramy to głosowanie
     */
    protected function supports(string $attribute, $subject): bool
    {
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::DELETE], true)) {
            return false;
        }

        return $subject instanceof Post;
    }

    /**
     * Właściwa logika dostępu.
     *
     * @param string         $attribute Atrybut (VIEW/EDIT/DELETE)
     * @param Post           $subject   Post
     * @param TokenInterface $token     Token użytkownika
     *
     * @return bool Czy zezwolić na dostęp
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // Niezalogowany – odmawiamy (VIEW opublikowanym ogarniemy niżej)
            // tu wrócimy 'false', ale logikę "opublikowany dla wszystkich"
            // rozwiążemy w kontrolerze przez brak IsGranted dla list/publicznych stron,
            // natomiast dla show() użyjemy votera:
            // VIEW: opublikowany -> true nawet dla anonima? Jeśli chcesz:
            // odkomentuj poniższe 3 linie:
            // if (self::VIEW === $attribute) {
            //     return $subject->getStatus() === 'published';
            // }
            return false;
        }

        // Admin ma pełny dostęp
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        $isAuthor = method_exists($subject, 'getAuthor') && $subject->getAuthor() === $user;

        return match ($attribute) {
            self::VIEW   => $subject->getStatus() === 'published' || $isAuthor,
            self::EDIT,
            self::DELETE => $isAuthor,
            default      => false,
        };
    }
}
