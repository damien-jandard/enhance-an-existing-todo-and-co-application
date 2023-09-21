<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class UserHandler implements UserHandlerInterface
{
    public function __construct(
        private TaskRepository $taskRepository,
        private UserRepository $userRepository,
        private $admin_email,
        private RequestStack $requestStack
    ) {
    }

    public function __invoke(User $user): void
    {
        $tasks = $this->taskRepository->findBy(['user' => $user]);
        if (!in_array($user->getEmail(), explode(", ", $this->admin_email))) {
            foreach ($tasks as $task) {
                $task->setUser(null);
                $this->taskRepository->save($task, true);
            }
            $this->userRepository->remove($user, true);
            $this->requestStack->getSession()->getFlashBag()->add(
                'success',
                'L\'utilisateur a bien été supprimé'
            );
        } else {
            $this->requestStack->getSession()->getFlashBag()->add(
                'danger',
                'Vous ne pouvez pas supprimer cet utilisateur'
            );
        }
    }
}
