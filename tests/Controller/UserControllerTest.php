<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
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
    
    public function testUserListWhenNotLoggedIn(): void
    {
        $this->client->request(Request::METHOD_GET, '/users');
        
        $this->assertResponseRedirects();

        $this->client->followRedirect();

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertRouteSame('login');
        $this->assertSelectorTextContains('h2', 'Connexion');
    }

    public function testUserListWhenNotAdmin(): void
    {
        $this->login('user');
        $this->client->request(Request::METHOD_GET, '/users');

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        $this->assertRouteSame('user_list');
    }

    public function testUserListWhenAdmin(): void
    {
        $this->login('admin');
        $this->client->request(Request::METHOD_GET, '/users');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertRouteSame('user_list');
        $this->assertSelectorTextContains('h1', 'Liste des utilisateurs');
    }

    public function testUserCreatedSuccessfully(): void
    {
        $this->login('admin');
        $crawler = $this->client->request(Request::METHOD_GET, '/users/create');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertRouteSame('user_create');
        $this->assertSelectorTextContains('h2', 'Ajout d\'un utilisateur');

        $form = $crawler->selectButton('Ajouter')->form([
            'user[username]' => 'add_user',
            'user[password][first]' => 'Add1234*',
            'user[password][second]' => 'Add1234*',
            'user[email]' => 'add@user.com',
            'user[roles]' => ['ROLE_USER']
        ]);
        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertRouteSame('user_create');
        $this->assertResponseRedirects();

        $this->client->followRedirect();
        
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertRouteSame('user_list');
        $this->assertSelectorTextContains('h1', 'Liste des utilisateurs');
        $this->assertSelectorExists('div.alert.alert-success');
    }

    public function testUserUpdatedSuccessfully(): void
    {
        $this->login('admin');
        $editUser = static::getContainer()->get(UserRepository::class)->findOneByUsername('add_user');
        $crawler = $this->client->request(Request::METHOD_GET, '/users/' . $editUser->getId() . '/edit');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertRouteSame('user_edit');
        $this->assertSelectorTextContains('h2', 'Modification d\'un utilisateur');
        $this->assertInputValueSame('user[username]', $editUser->getUsername());
        $this->assertInputValueSame('user[email]', $editUser->getEmail());

        $form = $crawler->selectButton('Modifier')->form([
            'user[username]' => 'update_user',
            'user[password][first]' => 'Update1234*',
            'user[password][second]' => 'Update1234*',
            'user[email]' => 'update@user.com',
            'user[roles]' => ['ROLE_USER']
        ]);
        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertRouteSame('user_edit');
        $this->assertResponseRedirects();

        $this->client->followRedirect();

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertRouteSame('user_list');
        $this->assertSelectorTextContains('h1', 'Liste des utilisateurs');
        $this->assertSelectorExists('div.alert.alert-success');
    }

    public function testAdminUpdatedSuccessfully(): void
    {
        $user = $this->login('admin');
        $crawler = $this->client->request(Request::METHOD_GET, '/users/' . $user->getId() . '/edit');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertRouteSame('user_edit');
        $this->assertSelectorTextContains('h2', 'Modification d\'un utilisateur');
        $this->assertInputValueSame('user[username]', $user->getUsername());
        $this->assertInputValueSame('user[email]', $user->getEmail());

        $form = $crawler->selectButton('Modifier')->form([
            'user[username]' => 'admin',
            'user[password][first]' => 'Admin1234*',
            'user[password][second]' => 'Admin1234*',
            'user[email]' => 'admin@replace-me.com',
            'user[roles]' => ['ROLE_USER']
        ]);
        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertRouteSame('user_edit');
        $this->assertResponseRedirects();

        $this->client->followRedirect();

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertRouteSame('user_list');
        $this->assertSelectorTextContains('h1', 'Liste des utilisateurs');
        $this->assertSelectorExists('div.alert.alert-success');
    }

    public function testUserCannotBeDelete(): void
    {
        $user = $this->login('admin');
        $this->client->request(Request::METHOD_POST, '/users/' . $user->getId() . '/delete');

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertRouteSame('user_delete');
        $this->assertResponseRedirects();

        $this->client->followRedirect();

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertRouteSame('user_list');
        $this->assertSelectorTextContains('h1', 'Liste des utilisateurs');
        $this->assertSelectorExists('div.alert.alert-danger');
    }

    public function testUserDeletedSuccessfully(): void
    {
        $this->login('admin');
        $deleteUser = static::getContainer()->get(UserRepository::class)->findOneByUsername('update_user');
        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $task = (new Task)
            ->setTitle('Tâche update_user')
            ->setContent('Contenu de la tâche pour update_user')
            ->setUser($deleteUser);
        $deleteUser->addTask($task);
        $taskRepository->save($task, true);
        $this->client->request(Request::METHOD_POST, '/users/' . $deleteUser->getId() . '/delete');

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertRouteSame('user_delete');
        $this->assertResponseRedirects();

        $this->client->followRedirect();

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertRouteSame('user_list');
        $this->assertSelectorTextContains('h1', 'Liste des utilisateurs');
        $this->assertSelectorExists('div.alert.alert-success');
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $this->client = null;
    }
}
