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

    public function testShouldFilterVideoGamesByTag(): void
    {
        $this->get('/');
        $this->assertResponseIsSuccessful();
        $sort = $this->get('/')->selectButton('Trier')->form();
        $sort['limit'] = '100';
        $this->client->submit($sort);
        $this->assertResponseIsSuccessful();

        // // Envoi du formulaire de tri par tag
        $form = $this->get('/')->selectButton('Filtrer')->form();
        $form['filter[tags]'] = ['1'];

        $this->client->submit($form);
        $this->assertSelectorCount(13, 'article.game-card');
    }

    // public function provideVideoGameTags(): array
    // {
    //     return [
    //         'multijoueur' => [['120' => 'multijoueur'], 13],
    //         'solo' => [['121' => 'solo'], 17],
    //         'en ligne' => [['122' => 'en ligne'], 11],
    //         'hors ligne' => [['123' => 'hors ligne'], 15],
    //         'coopératif' => [['124' => 'coopératif'], 17],
    //         'compétitif' => [['125' => 'compétitif'], 19],
    //     ];
    // }

}
