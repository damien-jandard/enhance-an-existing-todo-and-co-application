<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;

class UserHandler implements UserHandlerInterface
{   
    public function __construct(
        private TaskRepository $taskRepository,
        private UserRepository $userRepository
    ) {
    }
    
    public function __invoke(User $user): void
    {
        $tasks = $this->taskRepository->findBy(['user' => $user]);
        foreach ($tasks as $task) {
            $task->setUser(null);
            $this->taskRepository->save($task, true);
        }
        $this->userRepository->remove($user, true);
    }
}
