<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    private ?KernelBrowser $client = null;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testLoginPage(): void
    {
        $this->client->request(Request::METHOD_GET, '/login');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h2', 'Connexion');
        $this->assertSelectorNotExists('div.alert.alert-danger');
    }

    public function testLoginWithBadCredentials(): void
    {
        $crawler = $this->client->request(Request::METHOD_GET, '/login');
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'username',
            '_password' => '12345678'
        ]);
        $this->client->submit($form);

        $this->assertResponseRedirects();

        $this->client->followRedirect();

        $this->assertRouteSame('login');
        $this->assertSelectorExists('div.alert.alert-danger');
    }

    public function testLoginSuccessfull(): void
    {
        $crawler = $this->client->request(Request::METHOD_GET, '/login');
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'user',
            '_password' => 'User1234*'
        ]);
        $this->client->submit($form);

        $this->assertResponseRedirects();

        $this->client->followRedirect();

        $this->assertRouteSame('homepage');
        $this->assertSelectorTextContains(
            'h2',
            'Bienvenue sur Todo List, l\'application vous permettant de gérer l\'ensemble de vos tâches sans effort !'
        );
    }

    public function testLogout(): void
    {
        $user = static::getContainer()->get(UserRepository::class)->findOneByUsername('user');
        $this->client->loginUser($user);
        $this->client->request(Request::METHOD_GET, '/logout');

        $this->assertResponseRedirects();

        $this->client->followRedirect();

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertRouteSame('homepage');
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $this->client = null;
    }
}
