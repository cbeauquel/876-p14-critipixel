<?php

declare(strict_types=1);

namespace App\Tests\Functional\Auth;

use App\Model\Entity\User;
use App\Tests\Functional\FunctionalTestCase;
use Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class RegisterTest extends FunctionalTestCase
{
    public function testThatRegistrationShouldSucceeded(): void
    {
        $this->get('/auth/register');

        $this->client->submitForm('S\'inscrire', self::getFormData());

        self::assertResponseRedirects('/auth/login');

        $user = $this->getEntityManager()->getRepository(User::class)->findOneByEmail('user@email.com');

        $userPasswordHasher = $this->service(UserPasswordHasherInterface::class);

        self::assertNotNull($user);
        self::assertSame('username', $user->getUsername());
        self::assertSame('user@email.com', $user->getEmail());
        self::assertTrue($userPasswordHasher->isPasswordValid($user, 'SuperPassword123!'));
    }

    /**
     * @dataProvider provideInvalidFormData
     * @param array<string> $formData
     */
    public function testThatRegistrationShouldFailed(array $formData): void
    {
        $this->get('/auth/register');
        $this->client->submitForm('S\'inscrire', $formData);
        $this->assertResponseStatusCodeSame(422);
    }

    /**
     * @return array<int, list<array<string, string>>>
     */
    public static function provideInvalidFormData(): array
    {
        return [
            [['register[username]' => '']],
            [['register[username]' => 'user+1']],
            [['register[username]' => 'Lorem ipsum dolor sit amet orci aliquam']],
            [['register[email]' => '']],
            [['register[email]' => 'user+1@email.com']],
            [['register[email]' => 'fail']],
        ];
    }

    /**
     * @param string[] $overrideData
     * @return string[]
     */
    public static function getFormData(array $overrideData = []): array
    {
        return [
            'register[username]' => 'username',
            'register[email]' => 'user@email.com',
            'register[plainPassword]' => 'SuperPassword123!'
        ] + $overrideData;
    }
}
