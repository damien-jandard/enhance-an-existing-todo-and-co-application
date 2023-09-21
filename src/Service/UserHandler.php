<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;

class UserHandler implements UserHandlerInterface
{
    public function __construct(
        private TaskRepository $taskRepository,
        private UserRepository $userRepository,
        private string $admin_email,
        private RequestStack $requestStack
    ) {
    }

    public function __invoke(User $user): void
    {
        $tasks = $this->taskRepository->findBy(['user' => $user]);
        /** @var Session $session */
        $session = $this->requestStack->getSession();

        if (!in_array($user->getEmail(), explode(", ", $this->admin_email))) {
            foreach ($tasks as $task) {
                $task->setUser(null);
                $this->taskRepository->save($task, true);
            }
            $this->userRepository->remove($user, true);

            $session->getFlashBag()->add(
                'success',
                'L\'utilisateur a bien été supprimé'
            );
        } else {
            $session->getFlashBag()->add(
                'danger',
                'Vous ne pouvez pas supprimer cet utilisateur'
            );
        }
    }
}
