<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Model\Entity\User;
use App\Model\Entity\VideoGame;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DomCrawler\Crawler;
use App\Doctrine\Repository\VideoGameRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class FunctionalTestCase extends WebTestCase
{
    protected KernelBrowser $client;

    // private VideoGameRepository $videoGameRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        // $this->videoGameRepository = self::getContainer()->get(VideoGameRepository::class);
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->service(EntityManagerInterface::class);
    }

    /**
     * @template T
     * @param class-string<T> $id
     */
    protected function service(string $id): object
    {
        return $this->client->getContainer()->get($id);
    }

    /**
     * @param string[] $parameters
     */
    protected function get(string $uri, array $parameters = []): Crawler
    {
        return $this->client->request('GET', $uri, $parameters);
    }

    protected function login(string $email = 'user+2@email.com'): void
    {
        $user = $this->service(EntityManagerInterface::class)->getRepository(User::class)->findOneByEmail($email);

        $this->client->loginUser($user);
    }

    // protected function tagCount($tagIds): int
    // {
    //     $tagCount = $this->service(EntityManagerInterface::class)->getRepository(VideoGame::class)->countByTags($tagIds);
    //     return $tagCount;
    // }


}
