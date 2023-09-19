<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const ADMIN_REFERENCE = 'admin';
    public const USER_REFERENCE = 'user';
    private $userPasswordHasher;
    public $admin_email;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher, string $admin_email)
    {
        $this->userPasswordHasher = $userPasswordHasher;
        $this->admin_email = $admin_email;
    }

    public function load(ObjectManager $manager): void
    {
        $admins = explode(', ', $this->admin_email);
        foreach ($admins as $a) {
            $admin = new User();
            $admin->setUsername(substr($a, 0, strpos($a, '@')))
                ->setEmail($a)
                ->setPassword($this->userPasswordHasher->hashPassword($admin, 'Admin1234*'))
                ->setRoles(['ROLE_ADMIN']);
            $manager->persist($admin);
        }
        
        $user = new User();
        $user->setUsername('user')
            ->setEmail('user@todolist.com')
            ->setPassword($this->userPasswordHasher->hashPassword($user, 'User1234*'));
        $manager->persist($user);

        $manager->flush();

        $this->addReference(self::ADMIN_REFERENCE, $admin);
        $this->addReference(self::USER_REFERENCE, $user);
    }
}
