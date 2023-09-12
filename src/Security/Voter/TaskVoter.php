<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class TaskVoter extends Voter
{
    public const CAN_UPDATE = 'CAN_UPDATE';
    public const CAN_DELETE = 'CAN_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::CAN_UPDATE, self::CAN_DELETE])
            && $subject instanceof \App\Entity\Task;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::CAN_UPDATE:
                if ($subject->getUser() === $user || in_array('ROLE_ADMIN', $user->getRoles())) {
                    return true;
                }
                break;
            case self::CAN_DELETE:
                if ($subject->getUser() === $user || (in_array('ROLE_ADMIN', $user->getRoles()) && $subject->getUser() === null)) {
                    return true;
                }
                break;
        }

        return false;
    }
}
