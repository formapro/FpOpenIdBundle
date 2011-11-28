<?php
namespace Fp\OpenIdBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginControllerTest extends WebTestCase
{
    public function shouldRedirectToOpenIdProvider()
    {
        // workaround for lightopenid lib.
        $_SERVER['REQUEST_URI'] = 'test.example.com';
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $form = $crawler
            ->selectButton('Sign In')
            ->form(array(
                'openid_identifier' => 'https://www.google.com/accounts/o8/id'
            )
        );

        $this->assertContains(
            'Redirecting to https://www.google.com/accounts/o8/ud?openid.ns=http%3A%2F%2Fspecs.openid.net%2Fauth%2F2.0&openid.mode=checkid_setup',
            $client->submit($form)->text()
        );
    }
}