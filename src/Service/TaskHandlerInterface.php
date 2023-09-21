<?php

namespace App\Service;

use App\Entity\Task;
use App\Entity\User;

interface TaskHandlerInterface
{
    /**
     * @return array<Task>
     */
    public function __invoke(bool $admin, User $user, bool $all, bool $isDone): array;
}
