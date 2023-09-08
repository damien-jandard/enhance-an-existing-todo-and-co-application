<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TaskFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        /** @var User $admin */
        $admin = $this->getReference(UserFixtures::ADMIN_REFERENCE);
        /** @var User $user */
        $user = $this->getReference(UserFixtures::USER_REFERENCE);

        $tasks = [
            [
                'title' => 'Tâche n°1',
                'content' => 'Répondre aux e-mails professionnels.'
            ],
            [
                'title' => 'Tâche n°2',
                'content' => 'Préparer une présentation pour la réunion de l\'équipe à 14h.'
            ],
            [
                'title' => 'Tâche n°3',
                'content' => 'Faire 30 minutes d\'exercice physique.'
            ],
            [
                'title' => 'Tâche n°4',
                'content' => 'Faire les courses après le travail.'
            ],
            [
                'title' => 'Tâche n°5',
                'content' => 'Lire le premier chapitre du nouveau livre.'
            ]
        ];

        foreach ($tasks as $t) {
            $task = new Task();
            rand(0, 1) === 0 ? $task->setUser($user) : $task->setUser(null);
            $task->setTitle($t['title'])
                ->setContent($t['content']);
            $manager->persist($task);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
