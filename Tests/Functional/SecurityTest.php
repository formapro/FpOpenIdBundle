<?php
namespace Fp\OpenIdBundle\Tests\Functional;

use Fp\OpenIdBundle\Tests\Functional\FakeRelyingParty;

/**
 * @author Kotlyar Maksim <kotlyar.maksim@gmail.com>
 * @since 4/27/12
 */
class SecurityTest extends WebTestCase
{
    /**
     * @test
     */
    public function shouldShowPageWithLoginForm()
    {
        $client = $this->createClient();
        
        $crawler = $client->request('GET', '/login_openid');
        
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertCount(1, $crawler->filter('form#fp_openid_login'));
    }

    /**
     * @test
     */
    public function shouldRedirectToOpenIdProviderSiteAfterLoginFormSubmit()
    {
        $client = $this->createClient();

        $crawler = $client->request('GET', '/login_openid');
        
        $form = $crawler->selectButton('Login')->form();
        $form['openid_identifier'] = FakeRelyingParty::VERIFY_IDENTIFIER;
        
        $client->submit($form);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertEquals(
            FakeRelyingParty::REDIRECT_URL, 
            $client->getResponse()->headers->get('Location')
        );
    }

    /**
     * @test
     */
    public function shouldSetSubmittedTargetPathToSession()
    {
        $client = $this->createClient();

        $crawler = $client->request('GET', '/login_openid');

        $client->request('POST', '/login_check_openid', array(
            'openid_identifier' => FakeRelyingParty::VERIFY_IDENTIFIER,
            '_custom_target_path' => '/the_target_path_url',
        ));

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        
        /** @var $session \Symfony\Component\HttpFoundation\Session\SessionInterface */
        $session = $client->getContainer()->get('session');
        $this->assertTrue($session->has('_security.main.target_path'));
        $this->assertEquals('/the_target_path_url', $session->get('_security.main.target_path'));
    }

    /**
     * @test
     */
    public function shouldRedirectToDefaultTargetOnCompleteRequest()
    {
        $client = $this->createClient();

        $client->request('GET', '/login_openid');
        $client->request('GET', '/login_check_openid?'. FakeRelyingParty::COMPLETE_REQUEST .'=1');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertEquals(
            'http://localhost/target_path',
            $client->getResponse()->headers->get('Location')
        );
    }

    /**
     * @test
     * 
     * @expectedException Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @expectedExceptionMessage Access Denied
     */
    public function shouldNotAllowAccessSecuredPageIfNotAuthenticated()
    {
        $client = $this->createClient();
        
        $client->request('GET', '/secured_page');
    }

    /**
     * @test
     */
    public function shouldAllowAccessSecuredPageIfAuthenticatedSuccessfully()
    {
        $client = $this->createClient();

        $client->request('GET', '/login_openid');
        $client->request('GET', '/login_check_openid?'. FakeRelyingParty::COMPLETE_REQUEST .'=1');
        $client->request('GET', '/secured_page');
        
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('Secured Content', $client->getResponse()->getContent());
    }
}
