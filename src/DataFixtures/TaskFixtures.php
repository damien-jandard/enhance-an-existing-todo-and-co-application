<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
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
                'content' => 'Gérer les comptes utilisateurs.'
            ],
            [
                'title' => 'Tâche n°2',
                'content' => 'Rédiger un rapport mensuel sur les ventes.'
            ],
            [
                'title' => 'Tâche n°3',
                'content' => 'Planifier et organiser la réunion d\'équipe hebdomadaire.'
            ],
            [
                'title' => 'Tâche n°4',
                'content' => 'Développer de nouvelles fonctionnalités pour l\'application client.'
            ],
            [
                'title' => 'Tâche n°5',
                'content' => 'Effectuer des recherches de marché pour identifier de nouvelles opportunités.'
            ],
            [
                'title' => 'Tâche n°6',
                'content' => 'Répondre aux demandes de support client par email et par téléphone.'
            ],
            [
                'title' => 'Tâche n°7',
                'content' => 'Mettre à jour la documentation technique du projet.'
            ],
            [
                'title' => 'Tâche n°8',
                'content' => 'Effectuer des tests de qualité sur le produit en développement.'
            ],
            [
                'title' => 'Tâche n°9',
                'content' => 'Préparer une présentation pour le prochain comité de direction.'
            ],
            [
                'title' => 'Tâche n°10',
                'content' => 'Collaborer avec l\'équipe marketing pour lancer une nouvelle campagne publicitaire.'
            ]
        ];

        foreach ($tasks as $t) {
            $task = new Task();
            if ($t['title'] === 'Tâche n°1') {
                $task->setUser($admin);
            } else {
                rand(0, 1) === 0 ? $task->setUser($user) : $task->setUser(null);
            }
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
