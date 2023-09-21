<?php

namespace App\Tests\Repository;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskRepositoryTest extends KernelTestCase
{
    private ?TaskRepository $taskRepository = null;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $this->taskRepository = $container->get(TaskRepository::class);
    }

    public function testSaveTask(): void
    {
        $task = new Task();
        $task->setTitle('Tâche de test')
            ->setContent('Contenu de ma tâche de test')
            ->toggle(false);

        $this->taskRepository->save($task, true);

        $newTask = $this->taskRepository->findOneBy(['title' => 'Tâche de test']);

        $this->assertInstanceOf(Task::class, $newTask);
    }

    public function testRemoveTask(): void
    {
        $task = $this->taskRepository->findOneBy(['title' => 'Tâche de test']);

        $this->assertInstanceOf(Task::class, $task);

        $this->taskRepository->remove($task, true);

        $this->assertNull($this->taskRepository->findOneBy(['title' => 'Tâche de test']));
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $this->taskRepository = null;
    }
}
