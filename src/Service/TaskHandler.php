<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\TaskRepository;
use App\Service\TaskHandlerInterface;

class TaskHandler implements TaskHandlerInterface
{
    public function __construct(
        private TaskRepository $taskRepository
    ) {
    }
    
    public function __invoke(
        bool $admin,
        User $user,
        bool $all = true,
        bool $isDone = false
    ): array {
        if ($all) {
            if ($admin) {
                $tasks = $this->taskRepository->findAll();
            } else {
                $tasks = $this->taskRepository->findBy(['user' => $user]);
            }
        } else {
            if ($admin) {
                $tasks = $this->taskRepository->findBy(['isDone' => $isDone]);
            } else {
                $tasks = $this->taskRepository->findBy(['user' => $user, 'isDone' => $isDone]);
            }
        }
        return $tasks;
    }
}
