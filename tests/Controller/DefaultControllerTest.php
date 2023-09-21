<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    private ?KernelBrowser $client = null;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function login(string $username): ?User
    {
        $user = static::getContainer()->get(UserRepository::class)->findOneByUsername($username);
        $this->client->loginUser($user);

        return $user;
    }

    public function testHomepageWhenNotLoggedIn(): void
    {
        $this->client->request(Request::METHOD_GET, '/');

        $this->assertResponseRedirects();

        $this->client->followRedirect();

        $this->assertRouteSame('login');
        $this->assertSelectorTextContains('h2', 'Connexion');
    }

    public function testHomepageWhenLoggedIn(): void
    {
        $this->login('user');
        $this->client->request(Request::METHOD_GET, '/');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertRouteSame('homepage');
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $this->client = null;
    }
}
