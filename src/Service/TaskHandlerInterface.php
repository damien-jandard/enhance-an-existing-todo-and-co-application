<?php

namespace App\Service;

use App\Entity\User;

interface TaskHandlerInterface
{
    public function __invoke(bool $admin, User $user, bool $all, bool $isDone): array;
}
