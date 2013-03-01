<?php
namespace Fp\OpenIdBundle\Tests\RelyingParty;

use Fp\OpenIdBundle\Security\Core\Authentication\Token\OpenIdToken;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

use Fp\OpenIdBundle\RelyingParty\RecoveredFailureRelyingParty;
use Fp\OpenIdBundle\RelyingParty\IdentityProviderResponse;

/**
 * @author Kotlyar Maksim <kotlyar.maksim@gmail.com>
 * @since 4/12/12
 */
class RecoveredFailureRelyingPartyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldImplementsRelyingPartyInterface()
    {
        $rc = new \ReflectionClass('Fp\OpenIdBundle\RelyingParty\RecoveredFailureRelyingParty');

        $this->assertTrue($rc->implementsInterface('Fp\OpenIdBundle\RelyingParty\RelyingPartyInterface'));
    }

    /**
     * @test
     */
    public function couldBeConstructedWithoutAnyArguments()
    {
        new RecoveredFailureRelyingParty;
    }

    /**
     * @test
     */
    public function shouldNotSupportIfRequestWithoutRecoveredQueryParameterSet()
    {
        $request = $this->createRequestMock();
        $request
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo(RecoveredFailureRelyingParty::RECOVERED_QUERY_PARAMETER))
            ->will($this->returnValue(null))
        ;

        $relyingParty = new RecoveredFailureRelyingParty;

        $this->assertFalse($relyingParty->supports($request));
    }

    /**
     * @test
     */
    public function shouldNotSupportIfSessionWithoutAuthenticationErrorSet()
    {
        $session = $this->createSessionMock();
        $session
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo(SecurityContextInterface::AUTHENTICATION_ERROR))
            ->will($this->returnValue(null))
        ;

        $request = $this->createRequestStub($returnGet = 1, $returnSession = $session);

        $relyingParty = new RecoveredFailureRelyingParty;

        $this->assertFalse($relyingParty->supports($request));
    }

    /**
     * @test
     */
    public function shouldNotSupportIfAuthenticationErrorWithoutIdentityProviderResponseSet()
    {
        $error = new AuthenticationException('an error');

        $session = $this->createSessionStub($returnGet = $error);
        $request = $this->createRequestStub($returnGet = 1, $returnSession = $session);

        $relyingParty = new RecoveredFailureRelyingParty;

        $this->assertFalse($relyingParty->supports($request));
    }

    /**
     * @test
     */
    public function shouldSupportIfAuthenticationErrorWithOpenIdTokenProviderResponseSet()
    {
        $token = new OpenIdToken('aProviderKey', 'identity');

        $error = new AuthenticationException('an error');
        $error->setToken($token);

        $session = $this->createSessionStub($returnGet = $error);
        $request = $this->createRequestStub($returnGet = 1, $returnSession = $session);

        $relyingParty = new RecoveredFailureRelyingParty;

        $this->assertTrue($relyingParty->supports($request));
    }

    /**
     * @test
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The relying party does not support the request
     */
    public function throwIfTryManageNotSupportedRequest()
    {
        $relyingParty = new RecoveredFailureRelyingParty;

        //guard
        $this->assertFalse($relyingParty->supports($this->createRequestMock()));

        $relyingParty->manage($this->createRequestMock());
    }

    /**
     * @test
     */
    public function shouldReturnIdentityProviderResponseOnManage()
    {
        $expectedIdentity = 'theIdentity';
        $expectedAttributes = array('foo' => 'fooVal', 'bar' => 'barVal');
        
        $token = new OpenIdToken('aProviderKey', $expectedIdentity);
        $token->setAttributes($expectedAttributes);

        $error = new AuthenticationException('an error');
        $error->setToken($token);

        $session = $this->createSessionStub($returnGet = $error);
        $request = $this->createRequestStub($returnGet = 1, $returnSession = $session);

        $relyingParty = new RecoveredFailureRelyingParty;

        //guard
        $this->assertTrue($relyingParty->supports($request));

        $actualIdentityProviderResponse = $relyingParty->manage($request);
        $this->assertInstanceOf('Fp\OpenIdBundle\RelyingParty\IdentityProviderResponse', $actualIdentityProviderResponse);
        $this->assertEquals($expectedIdentity,$actualIdentityProviderResponse->getIdentity());
        $this->assertEquals($expectedAttributes,$actualIdentityProviderResponse->getAttributes());
    }

    /**
     * @test
     */
    public function shouldRemoveErrorFromSessionOnManage()
    {        
        $error = new AuthenticationException('an error');
        $error->setToken(new OpenIdToken('aProviderKey', 'anIdentity'));

        $session = $this->createSessionStub($returnGet = $error);
        $session
            ->expects($this->once())
            ->method('remove')
            ->with($this->equalTo(SecurityContextInterface::AUTHENTICATION_ERROR))
        ;

        $request = $this->createRequestStub($returnGet = 1, $returnSession = $session);

        $relyingParty = new RecoveredFailureRelyingParty;

        //guard
        $this->assertTrue($relyingParty->supports($request));

        $relyingParty->manage($request);
    }

    public function createRequestMock()
    {
        return $this->getMock('Symfony\Component\HttpFoundation\Request', array(), array(), '', false);
    }

    public function createRequestStub($returnGet = null, $returnSession = null)
    {
        $request = $this->createRequestMock();

        $request
            ->expects($this->any())
            ->method('get')
            ->will($this->returnValue($returnGet))
        ;
        $request
            ->expects($this->any())
            ->method('getSession')
            ->will($this->returnValue($returnSession))
        ;

        return $request;
    }

    public function createSessionMock()
    {
        return $this->getMock('Symfony\Component\HttpFoundation\Session\SessionInterface');
    }

    public function createSessionStub($returnGet = null)
    {
        $session = $this->createSessionMock();

        $session
            ->expects($this->any())
            ->method('get')
            ->will($this->returnValue($returnGet))
        ;

        return $session;
    }
}
