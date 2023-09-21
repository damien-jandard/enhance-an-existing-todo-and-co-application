<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    private ?KernelBrowser $client = null;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function login(string $username): ?User
    {
        $user = static::getContainer()->get(UserRepository::class)->findOneByUsername($username);
        $this->client->loginUser($user);

        return $user;
    }

    public function testTaskListWhenNotLoggedIn(): void
    {
        $this->client->request(Request::METHOD_GET, '/tasks');

        $this->assertResponseRedirects();

        $this->client->followRedirect();

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertRouteSame('login');
        $this->assertSelectorTextContains('h2', 'Connexion');
    }

    public function testTaskListWhenUserLoggedIn(): void
    {
        $this->login('user');
        $this->client->request(Request::METHOD_GET, '/tasks');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertRouteSame('task_list');
        $this->assertSelectorTextContains('h2', 'Liste de toutes les tâches');
    }

    public function testTaskListWhenAdminLoggedIn(): void
    {
        $this->login('admin');
        $this->client->request(Request::METHOD_GET, '/tasks');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertRouteSame('task_list');
        $this->assertSelectorTextContains('h2', 'Liste de toutes les tâches');
    }

    public function testTaskTodoWhenNotLoggedIn(): void
    {
        $this->client->request(Request::METHOD_GET, '/tasks/todo');

        $this->assertResponseRedirects();

        $this->client->followRedirect();

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertRouteSame('login');
        $this->assertSelectorTextContains('h2', 'Connexion');
    }

    public function testTaskTodoWhenUserLoggedIn(): void
    {
        $this->login('user');
        $this->client->request(Request::METHOD_GET, '/tasks/todo');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertRouteSame('task_todo');
        $this->assertSelectorTextContains('h2', 'Liste des tâches à faire');
    }

    public function testTaskTodoWhenAdminLoggedIn(): void
    {
        $this->login('admin');
        $this->client->request(Request::METHOD_GET, '/tasks/todo');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertRouteSame('task_todo');
        $this->assertSelectorTextContains('h2', 'Liste des tâches à faire');
    }

    public function testTaskDoneWhenNotLoggedIn(): void
    {
        $this->client->request(Request::METHOD_GET, '/tasks/done');

        $this->assertResponseRedirects();

        $this->client->followRedirect();

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertRouteSame('login');
        $this->assertSelectorTextContains('h2', 'Connexion');
    }

    public function testTaskDoneWhenUserLoggedIn(): void
    {
        $this->login('user');
        $this->client->request(Request::METHOD_GET, '/tasks/done');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertRouteSame('task_done');
        $this->assertSelectorTextContains('h2', 'Liste des tâches terminées');
    }

    public function testTaskDoneWhenAdminLoggedIn(): void
    {
        $this->login('user');
        $this->client->request(Request::METHOD_GET, '/tasks/done');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertRouteSame('task_done');
        $this->assertSelectorTextContains('h2', 'Liste des tâches terminées');
    }

    public function testTaskCreatedSuccessfully(): void
    {
        $this->login('user');
        $crawler = $this->client->request(Request::METHOD_GET, '/tasks/create');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertRouteSame('task_create');
        $this->assertSelectorTextContains('h2', 'Ajout d\'une tâche');

        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]' => 'Tâche de test',
            'task[content]' => 'Contenu de la tâche de test'
        ]);
        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertRouteSame('task_create');
        $this->assertResponseRedirects();

        $this->client->followRedirect();

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertRouteSame('task_list');
        $this->assertSelectorTextContains('h2', 'Liste de toutes les tâches');
        $this->assertSelectorExists('div.alert.alert-success');
    }

    public function testTaskUpdatedSuccessfully(): void
    {
        $user = $this->login('user');
        $task = $user->getTasks()->last();
        $crawler = $this->client->request(Request::METHOD_GET, '/tasks/' . $task->getId() . '/edit');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertRouteSame('task_edit');
        $this->assertSelectorTextContains('h2', 'Modification d\'une tâche');
        $this->assertInputValueSame('task[title]', $task->getTitle());
        $this->assertSelectorTextSame('textarea[name="task[content]"]', $task->getContent());

        $form = $crawler->selectButton('Modifier')->form([
            'task[content]' => 'Contenu de la tâche de test (Mise à jour)'
        ]);
        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertRouteSame('task_edit');
        $this->assertResponseRedirects();

        $this->client->followRedirect();

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertRouteSame('task_list');
        $this->assertSelectorTextContains('h2', 'Liste de toutes les tâches');
        $this->assertSelectorExists('div.alert.alert-success');
    }

    public function testTaskCannotBeEdit(): void
    {
        $this->login('user');
        $admin = static::getContainer()->get(UserRepository::class)->findOneByUsername('admin2');
        $task = $admin->getTasks()->first();
        $this->client->request(Request::METHOD_GET, '/tasks/' . $task->getId() . '/edit');

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testTaskCompleted(): void
    {
        $user = $this->login('user');
        $task = $user->getTasks()->last();
        $this->client->request(Request::METHOD_GET, '/tasks/' . $task->getId() . '/toggle');

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertRouteSame('task_toggle');
        $this->assertResponseRedirects();

        $this->client->followRedirect();

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertRouteSame('task_list');
        $this->assertSelectorTextContains('h2', 'Liste de toutes les tâches');
        $this->assertSelectorExists('div.alert.alert-success');
    }

    public function testTaskToDo(): void
    {
        $user = $this->login('user');
        $task = $user->getTasks()->last();
        $this->client->request(Request::METHOD_GET, '/tasks/' . $task->getId() . '/toggle');

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertRouteSame('task_toggle');
        $this->assertResponseRedirects();

        $this->client->followRedirect();

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertRouteSame('task_list');
        $this->assertSelectorTextContains('h2', 'Liste de toutes les tâches');
        $this->assertSelectorExists('div.alert.alert-success');
    }

    public function testTaskDeletedSuccessfully(): void
    {
        $user = $this->login('user');
        $task = $user->getTasks()->last();
        $this->client->request(Request::METHOD_GET, '/tasks/' . $task->getId() . '/delete');

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertRouteSame('task_delete');
        $this->assertResponseRedirects();

        $this->client->followRedirect();

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertRouteSame('task_list');
        $this->assertSelectorTextContains('h2', 'Liste de toutes les tâches');
        $this->assertSelectorExists('div.alert.alert-success');
    }

    public function testTaskCannotBeDelete(): void
    {
        $this->login('user');
        $admin = static::getContainer()->get(UserRepository::class)->findOneByUsername('admin2');
        $task = $admin->getTasks()->first();
        $this->client->request(Request::METHOD_GET, '/tasks/' . $task->getId() . '/delete');

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $this->client = null;
    }
}
