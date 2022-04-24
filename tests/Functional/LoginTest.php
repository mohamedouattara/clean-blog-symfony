<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class LoginTest extends WebTestCase
{

    public function testIfLoginIsSuccessful(): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $crawler = $client->request(Request::METHOD_GET, $router->generate('security_login'));

        $form = $crawler->filter('form[name="login"]')->form([
            'email' => 'admin@email.com',
            'password' => 'password'
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
        $this->assertRouteSame('index');
    }

    /** @dataProvider provideInvalidCredentials */
    public function testIfCredentialsAreInvalid(string $email, string $password): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $crawler = $client->request(Request::METHOD_GET, $router->generate('security_login'));

        $form = $crawler->filter('form[name="login"]')->form(['email' => $email, 'password' => $password]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        $this->assertSelectorTextContains('div.alert-danger', 'Identifiants invalides.');
    }

    public function provideInvalidCredentials(): iterable
    {
        yield ['admin@email.com', 'failpassword'];
        yield ['fail2@email.com', 'fapassword'];
    }


    public function testIfCsrfTokenIsInvalid(): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $crawler = $client->request(Request::METHOD_GET, $router->generate('security_login'));

        $form = $crawler->filter('form[name="login"]')->form(
            [
                '_csrf_token' => 'failtoken',
                'email' => 'email@email.com',
                'password' => 'password'
            ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        $this->assertSelectorTextContains('div.alert-danger', 'Jeton CSRF invalide.');
    }

    public function testIfUserAccountIsSuspended(): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $crawler = $client->request(Request::METHOD_GET, $router->generate('security_login'));

        $form = $crawler->filter('form[name="login"]')->form(
            [
                'email' => 'suspended@email.com',
                'password' => 'password'
            ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        $this->assertSelectorTextContains('div.alert-danger', 'Votre compte a été suspendu. Merci de contacter l\'administrateur.');
    }

}