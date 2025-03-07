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
     * @param string[] $formData
     */
    public function testThatRegistrationShouldFailed(array $formData): void
    {
        $this->get('/auth/register');

        $this->client->submitForm('S\'inscrire', $formData);

        self::assertResponseIsUnprocessable();
    }

    /**
     * @return Generator<array<int, array<string>>>
     */
    public static function provideInvalidFormData(): iterable
    {
        yield 'empty username' => array(self::getFormData(array('register[username]' => '')));
        yield 'non unique username' => array(self::getFormData(array('register[username]' => 'user+1')));
        yield 'too long username' => array(self::getFormData(array('register[username]' => 'Lorem ipsum dolor sit amet orci aliquam')));
        yield 'empty email' => array(self::getFormData(array('register[email]' => '')));
        yield 'non unique email' => array(self::getFormData(array('register[email]' => 'user+1@email.com')));
        yield 'invalid email' => array(self::getFormData(array('register[email]' => 'fail')));
    }

    /**
     * @param string[] $overrideData
     * @return string[]
     */
    public static function getFormData(array $overrideData = array()): array
    {
        return array(
            'register[username]' => 'username',
            'register[email]' => 'user@email.com',
            'register[plainPassword]' => 'SuperPassword123!'
        ) + $overrideData;
    }
}
