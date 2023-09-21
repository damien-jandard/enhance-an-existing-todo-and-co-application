<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    private ?UserRepository $userRepository = null;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $this->userRepository = $container->get(UserRepository::class);
    }

    public function testSaveUser(): void
    {
        $user = new User();
        $user->setUsername('user2')
            ->setRoles([])
            ->setPassword('0000')
            ->setEmail('user2@test.com');
        
        $this->userRepository->save($user, true);

        $newUser = $this->userRepository->findOneBy(['username' => 'user2']);

        $this->assertInstanceOf(User::class, $newUser);
    }

    public function testRemoveUser(): void
    {
        $user = $this->userRepository->findOneBy(['username' => 'user2']);

        $this->assertInstanceOf(User::class, $user);

        $this->userRepository->remove($user, true);

        $this->assertNull($this->userRepository->findOneBy(['username' => 'user2']));        
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $this->userRepository = null;
    }
}
