<?php

declare(strict_types=1);

namespace App\Tests\Functional\VideoGame;

use App\List\VideoGamesList;
use App\Model\ValueObject;
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
        $this->client->submitForm('Filtrer', ['filter[search]' => 'Jeu vidéo 49'], 'GET');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(1, 'article.game-card');
    }

    /**
     * @dataProvider provideVideoGameTags
     * @param array<int, array<int>> $tags
     * @param array<int|string, int|string> $tagIds
     */
    public function testShouldFilterVideoGamesByTag(array $tags, array $tagIds): void
    {
        // dd($tagIds);
        $tagCount = $this->tagCount($tagIds);
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
    * @return array<int, array<int, array<int|string, int|string>>>
    */
    public function provideVideoGameTags(): array
    {
        return [
            [['filter[tags][0]' => '1'], [1]],
            [['filter[tags][1]' => '2'], [2]],
            [['filter[tags][2]' => '3'], [3]],
            [['filter[tags][3]' => '4'], [4]],
            [['filter[tags][4]' => '5'], [5]],
            [['filter[tags][5]' => '6'], [6]],
            [['filter[tags][0]' => '1', 'filter[tags][1]' => '2'], [1, 2]],
            [['filter[tags][0]' => '1', 'filter[tags][2]' => '3'], [1, 3]],
            [['filter[tags][0]' => '1', 'filter[tags][5]' => '6'], [1, 6]],
            [['filter[tags][0]' => '1', 'filter[tags][1]' => '2', 'filter[tags][2]' => '3'], [1, 2, 3]],
            [['filter[tags][0]' => '1', 'filter[tags][1]' => '2', 'filter[tags][3]' => '4'], [1, 2, 4]],
            [['filter[tags][0]' => '1', 'filter[tags][1]' => '2', 'filter[tags][2]' => '3', 'filter[tags][3]' => '4'], [1, 2, 3, 4]],
        ];
    }
}
