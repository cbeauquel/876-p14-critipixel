<?php

namespace App\Doctrine\DataFixtures;

use App\Model\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use function array_fill_callback;

final class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $users = [];
        for ($i = 0; $i < 10; $i++) {
            $user = (new User())
                ->setEmail(sprintf('user+%d@email.com', $i))
                ->setPlainPassword('password')
                ->setUsername(sprintf('user+%d', $i));
            $manager->persist($user);

            $users[] = $user;
        };
        $manager->flush();
    }
}
