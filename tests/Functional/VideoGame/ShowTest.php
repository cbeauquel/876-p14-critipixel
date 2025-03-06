<?php

declare(strict_types=1);

namespace App\Tests\Functional\VideoGame;

use App\Tests\Functional\FunctionalTestCase;

final class ShowTest extends FunctionalTestCase
{
    public function testShouldShowVideoGame(): void
    {
        $this->get('/jeu-video-0');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Jeu vidéo 0');
    }

    public function testShouldAddReview(): void
    {
        // Connexion de l'utilisateur
        $this->login();
                       
        // Accès à la page du jeu vidéo
        $this->get('/jeu-video-0');
        $this->assertResponseIsSuccessful();
        
        // Envoi du formulaire de review
        $this->client->submitForm('Poster', array(
            'review[rating]' => '2',
            'review[comment]' => 'ceci est un test'
        ));
        
        // Vérification de la redirection après soumission du formulaire
        $this->assertResponseStatusCodeSame(302);
        
        $this->client->followRedirect();
               
        $this->assertSelectorTextContains('div.list-group-item:last-child h3', 'user+2');
        $this->assertSelectorTextContains('div.list-group-item:last-child p', 'ceci est un test');
        $this->assertSelectorTextContains('div.list-group-item:last-child span.value', '2');
    }
}
