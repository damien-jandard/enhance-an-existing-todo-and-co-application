<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use App\Security\Voter\TaskVoter;
use App\Service\TaskHandlerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/tasks', name: 'task_')]
class TaskController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function taskList(
        TaskHandlerInterface $taskHandler
    ): Response {
        return $this->render('task/list.html.twig', [
            'tasks' => $taskHandler($this->isGranted('ROLE_ADMIN'), $this->getUser(), true),
            'title' => 'Liste de toutes les tâches'
        ]);
    }

    #[Route('/todo', name: 'todo', methods: ['GET'])]
    public function taskListTodo(
        TaskHandlerInterface $taskHandler
    ): Response {
        return $this->render('task/list.html.twig', [
            'tasks' => $taskHandler($this->isGranted('ROLE_ADMIN'), $this->getUser(), false, false),
            'title' => 'Liste des tâches à faire'
        ]);
    }

    #[Route('/done', name: 'done', methods: ['GET'])]
    public function taskListDone(
        TaskHandlerInterface $taskHandler
    ): Response {
        return $this->render('task/list.html.twig', [
            'tasks' => $taskHandler($this->isGranted('ROLE_ADMIN'), $this->getUser(), false, true),
            'title' => 'Liste des tâches terminées'
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function taskCreate(
        Request $request,
        TaskRepository $taskRepository
    ): Response {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getUser()->addTask($task);
            $taskRepository->save($task, true);

            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/{id}/edit', name: 'edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    #[IsGranted(TaskVoter::CAN_UPDATE, subject: 'task')]
    public function taskEdit(
        Task $task,
        Request $request,
        TaskRepository $taskRepository
    ): Response {
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $taskRepository->save($task, true);

            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    #[Route('/{id}/toggle', name: 'toggle', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[IsGranted(TaskVoter::CAN_UPDATE, subject: 'task')]
    public function taskToggle(
        Task $task,
        TaskRepository $taskRepository
    ): Response {
        $task->toggle(!$task->isDone());
        $taskRepository->save($task, true);

        if ($task->isDone()) {
            $this->addFlash('success', sprintf('La tâche %s a été marquée comme faite.', $task->getTitle()));
        } else {
            $this->addFlash('success', sprintf('La tâche %s a été marquée comme à faire.', $task->getTitle()));
        }

        return $this->redirectToRoute('task_list');
    }

    #[Route('/{id}/delete', name: 'delete', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[IsGranted(TaskVoter::CAN_DELETE, subject: 'task')]
    public function taskDelete(
        Task $task,
        TaskRepository $taskRepository
    ): Response {
        if (null !== $task->getTitle()) {
            $this->getUser()->removeTask($task);
        }
        $taskRepository->remove($task, true);

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('task_list');
    }
}
