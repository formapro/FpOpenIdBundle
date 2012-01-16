<?php
namespace Fp\OpenIdBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginControllerTest extends WebTestCase
{
    /**
     * @test
     */
    public function shouldRedirectToOpenIdProvider()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $form = $crawler
            ->selectButton('Sign In')
            ->form(array(
                'openid_identifier' => 'an_id'
            )
        );

        $this->assertContains(
            'Redirecting to http://openid.provider.com?id=an_id&trust_root=localhost&return_url=http://localhost/login_check',
            $client->submit($form)->text()
        );
    }
}