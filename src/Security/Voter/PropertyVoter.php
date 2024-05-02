<?php

namespace App\Security\Voter;

use App\Entity\Property;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class PropertyVoter extends Voter
{
    public const LIST_ALL = 'PROPERTY_LIST_ALL';
    public const LIST = 'PROPERTY_LIST';
    public const CREATE = 'PROPERTY_CREATE';
    public const EDIT = 'PROPERTY_EDIT';
    public const VIEW = 'PROPERTY_VIEW';

    function __construct(
        private readonly Security $security
    )
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return 
            (in_array($attribute, [self::LIST_ALL, self::CREATE, self::LIST])) ||
            (
                in_array($attribute, [self::EDIT,  self::VIEW])
                && $subject instanceof \App\Entity\Property
            )
        ;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        /* if (!$subject instanceof Property) return false; */

        switch ($attribute) {
            case self::EDIT:
            case self::VIEW:
                return $subject->getUser()->getId() === $this->security->getUser()->getId();
            break;
            case self::CREATE:
            case self::LIST:
                return in_array('ROLE_USER', $this->security->getUser()->getRoles());
            break;
        }

        return false;
    }
}
