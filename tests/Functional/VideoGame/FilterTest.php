<?php

declare(strict_types=1);

namespace App\Tests\Functional\VideoGame;

use App\Tests\Functional\FunctionalTestCase;

final class FilterTest extends FunctionalTestCase
{
    public function testShouldListTenVideoGames(): void
    {
        $this->get('/');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(10, 'article.game-card');
        $this->client->clickLink('2');
        self::assertResponseIsSuccessful();
    }

    public function testShouldFilterVideoGamesBySearch(): void
    {
        $this->get('/');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(10, 'article.game-card');
        $this->client->submitForm('Filtrer', array('filter[search]' => 'Jeu vidéo 49'), 'GET');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(1, 'article.game-card');
    }

    /**
     * @dataProvider provideVideoGameTags
     * @param array<string|int> $tags
     */
    public function testShouldFilterVideoGamesByTag(array $tags, int $tagCount): void
    {
        $this->get('/');
        $this->assertResponseIsSuccessful();
        $sort = $this->get('/')->selectButton('Trier')->form();
        $sort['limit'] = '100';
        $this->client->submit($sort);
        ///test d'affichage du nb total de jeux vidéos
        self::assertSelectorCount(50, 'article.game-card');

        // // Envoi du formulaire de tri par tag
        $this->client->submitForm(
            'Filtrer',
            $tags,
            'GET'
        );

        $this->assertSelectorCount($tagCount, 'article.game-card');
    }

    /**
    * @return array<int, array<int, array<string, string>|int>>
    */
    public function provideVideoGameTags(): array
    {
        return array(
            array(array('filter[tags][0]' => '7'), 15),
            array(array('filter[tags][1]' => '8'), 25),
            array(array('filter[tags][2]' => '9'), 18),
            array(array('filter[tags][3]' => '10'), 13),
            array(array('filter[tags][4]' => '11'), 19),
            array(array('filter[tags][5]' => '12'), 21),
            array(array('filter[tags][0]' => '7', 'filter[tags][1]' => '8'), 10),
            array(array('filter[tags][0]' => '7', 'filter[tags][2]' => '9'), 4),
            array(array('filter[tags][0]' => '7', 'filter[tags][5]' => '12'), 6),
            array(array('filter[tags][0]' => '7', 'filter[tags][1]' => '8', 'filter[tags][2]' => '9'), 2),
            array(array('filter[tags][0]' => '7', 'filter[tags][1]' => '8', 'filter[tags][3]' => '10'), 3),
            array(array('filter[tags][0]' => '7', 'filter[tags][1]' => '8', 'filter[tags][2]' => '9', 'filter[tags][3]' => '10'), 0),

        );
    }
}
