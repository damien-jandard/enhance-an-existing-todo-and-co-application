<?php

namespace App\EventListener;

use Doctrine\ORM\Event\PreRemoveEventArgs;

class BlockUserDeletionListener
{
    public function __construct(
        public string $admin_email
    ) {
        
    }

    public function preRemove(PreRemoveEventArgs $event)
    {
        if ($event->getObject()->getEmail() === $this->admin_email) {
            throw new \Exception('La suppression de cet utilisateur est interdite.');
        }
    }
}
