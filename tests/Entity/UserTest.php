<?php

namespace App\Tests\Entity;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;

class UserTest extends KernelTestCase
{
    public function getEntity(): User
    {
        return (new User())
            ->setUsername('username')
            ->setRoles([])
            ->setPassword('0000')
            ->setEmail('username@todolist.com');
    }

    public function assertHasErrors(User $user, int $number = 0): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $errors = $container->get('validator')->validate($user);
        $messages = [];
        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
        }
        $this->assertCount($number, $errors, implode(', ', $messages));
    }

    public function testValidEntity(): void
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }

    public function testInvalidBlankUsernameEntity(): void
    {
        $this->assertHasErrors($this->getEntity()->setUsername(''), 1);
    }

    public function testInvalidBlankEmailEntity(): void
    {
        $this->assertHasErrors($this->getEntity()->setEmail(''), 1);
    }

    public function testUserEntity(): void
    {
        $user = $this->getEntity();

        $this->assertEquals('username', $user->getUsername());
        $this->assertEquals('username@todolist.com', $user->getEmail());
        $this->assertEquals('0000', $user->getPassword());
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
    }
}
