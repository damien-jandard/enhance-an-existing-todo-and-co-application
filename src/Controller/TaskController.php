<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use App\Service\TaskHandlerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/tasks', name: 'task_')]
class TaskController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function taskList(TaskHandlerInterface $taskHandler)
    {
        return $this->render('task/list.html.twig', [
            'tasks' => $taskHandler($this->isGranted('ROLE_ADMIN'), $this->getUser(), true)
        ]);
    }

    #[Route('/todo', name: 'todo', methods: ['GET'])]
    public function taskListTodo(TaskHandlerInterface $taskHandler)
    {
        return $this->render('task/list.html.twig', [
            'tasks' => $taskHandler($this->isGranted('ROLE_ADMIN'), $this->getUser(), false, false)
        ]);
    }

    #[Route('/done', name: 'done', methods: ['GET'])]
    public function taskListDone(TaskHandlerInterface $taskHandler)
    {
        return $this->render('task/list.html.twig', [
            'tasks' => $taskHandler($this->isGranted('ROLE_ADMIN'), $this->getUser(), false, true)
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function taskCreate(Request $request, TaskRepository $taskRepository)
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setUser($this->getUser());
            $taskRepository->save($task, true);

            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/{id}/edit', name: 'edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function taskEdit(Task $task, Request $request, TaskRepository $taskRepository)
    {
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
    public function taskToggle(Task $task, TaskRepository $taskRepository)
    {
        $task->toggle(!$task->isDone());
        $taskRepository->save($task, true);

        $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

        return $this->redirectToRoute('task_list');
    }

    #[Route('/{id}/delete', name: 'delete', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function taskDelete(Task $task, TaskRepository $taskRepository)
    {
        $taskRepository->remove($task, true);

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('task_list');
    }
}
