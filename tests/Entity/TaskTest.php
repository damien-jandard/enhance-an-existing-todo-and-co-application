<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskTest extends KernelTestCase
{
    private DateTimeImmutable $date;

    public function getEntity(): Task
    {
        $this->date = new \DateTimeImmutable();

        return (new Task())
            ->setUser(null)
            ->setCreatedAt($this->date)
            ->setTitle('Tâche de test')
            ->setContent('Contenu de la tâche de test')
            ->toggle(false);
    }

    public function assertHasErrors(Task $task, int $number = 0): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $error = $container->get('validator')->validate($task);

        $this->assertCount($number, $error);
    }

    public function testValidEntity(): void
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }

    public function testInvalidBlankTitleEntity(): void
    {
        $this->assertHasErrors($this->getEntity()->setTitle(''), 1);
    }

    public function testInvalidBlankContentEntity(): void
    {
        $this->assertHasErrors($this->getEntity()->setContent(''), 1);
    }

    public function testTaskEntity(): void
    {
        $task = $this->getEntity();

        $this->assertEquals('Tâche de test', $task->getTitle());
        $this->assertEquals('Contenu de la tâche de test', $task->getContent());
        $this->assertEquals(null, $task->getUser());
        $this->assertEquals($this->date, $task->getCreatedAt());
        $this->assertEquals(false, $task->isDone());
    }
}
