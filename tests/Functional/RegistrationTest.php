<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class RegistrationTest extends WebTestCase
{
    public function testIfRegistrationIsSuccessful()
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $crawler = $client->request(Request::METHOD_POST, $router->generate('app_registration'));

        $form = $crawler->filter('form[name="registration"]')->form([
            'registration[email]' => 'test@email.com',
            'registration[nickname]' => 'test',
            'registration[plainPassword]' => 'password',
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
        $this->assertRouteSame('index');
    }

    /**
     * @dataProvider provideInvalidFormData
     */
    public function testIfFormIsInvalid(array $formdata, string $errorMessage): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $crawler = $client->request(Request::METHOD_POST, $router->generate('app_registration'));

        $form = $crawler->filter('form[name="registration"]')->form($formdata);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertSelectorTextContains('.form-error-message', $errorMessage);
    }

    public function provideInvalidFormData(): iterable
    {
        yield [
            [
                'registration[email]' => '',
                'registration[nickname]' => 'test',
                'registration[plainPassword]' => 'password',
            ],
            "Cette valeur ne doit pas être vide."
        ];

        yield [
            [
                'registration[email]' => 'test@email.com',
                'registration[nickname]' => '',
                'registration[plainPassword]' => 'password',
            ],
            "Cette valeur ne doit pas être vide."
        ];

        yield [
            [
                'registration[email]' => 'test@email.com',
                'registration[nickname]' => 'test',
                'registration[plainPassword]' => '',
            ],
            "Cette valeur ne doit pas être vide."
        ];

        yield [
            [
                'registration[email]' => 'test@email.com',
                'registration[nickname]' => 'test',
                'registration[plainPassword]' => 'hello',
            ],
            "Cette chaîne est trop courte. Elle doit avoir au minimum 8 caractères."
        ];

        yield [
            [
                'registration[email]' => 'test',
                'registration[nickname]' => 'nico',
                'registration[plainPassword]' => 'password',
            ],
            "Cette valeur n'est pas une adresse email valide."
        ];

        yield [
            [
                'registration[email]' => 'admin@email.com',
                'registration[nickname]' => 'password1',
                'registration[plainPassword]' => 'password',
            ],
            "Cette valeur est déjà utilisée."
        ];

        yield [
            [
                'registration[email]' => 'email1@email.com',
                'registration[nickname]' => 'Roger',
                'registration[plainPassword]' => 'password',
            ],
            "Cette valeur est déjà utilisée."
        ];
    }

    public function testIfCsrfTokenIsInvalid(): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $crawler = $client->request(Request::METHOD_POST, $router->generate('app_registration'));

        $form = $crawler->filter('form[name="registration"]')->form(
            [
                'registration[_token]' => 'failtoken',
                'registration[email]' => 'test@email.com',
                'registration[nickname]' => 'test',
                'registration[plainPassword]' => 'password',
            ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertSelectorTextContains('.form-error-message', 'Le jeton CSRF est invalide. Veuillez renvoyer le formulaire.');
    }

}