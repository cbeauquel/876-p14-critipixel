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
        $this->client->submitForm('Filtrer', ['filter[search]' => 'Jeu vidéo 49'], 'GET');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(1, 'article.game-card');
    }

    /**
     * @dataProvider provideVideoGameTags
     */
    public function testShouldFilterVideoGamesByTag($tags, $tagCount): void
    {
        $this->get('/');
        $this->assertResponseIsSuccessful();
        $sort = $this->get('/')->selectButton('Trier')->form();
        $sort['limit'] = '100';
        $this->client->submit($sort);
        $this->assertResponseIsSuccessful();

        // // Envoi du formulaire de tri par tag
         $this->client->submitForm('Filtrer', 
            $tags, 
            'GET');

        $this->assertSelectorCount($tagCount, 'article.game-card');
    }

    public function provideVideoGameTags(): array
    {
        return [
            [['filter[tags][0]' => '7'], 15],
            [['filter[tags][1]' => '8'], 25],
            [['filter[tags][2]' => '9'], 18],
            [['filter[tags][3]' => '10'], 13],
            [['filter[tags][4]' => '11'], 19],
            [['filter[tags][5]' => '12'], 21],
        ];
    }

}
