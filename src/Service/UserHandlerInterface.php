<?php

namespace App\Service;

use App\Entity\User;

interface UserHandlerInterface
{
    public function __invoke(User $user): void;
}
