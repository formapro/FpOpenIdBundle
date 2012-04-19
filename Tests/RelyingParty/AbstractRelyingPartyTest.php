<?php
namespace Fp\OpenIdBundle\Tests\RelyingParty;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Fp\OpenIdBundle\RelyingParty\AbstractRelyingParty;
use Fp\OpenIdBundle\RelyingParty\IdentityProviderResponse;

/**
 * @author Kotlyar Maksim <kotlyar.maksim@gmail.com>
 * @since 4/19/12
 */
class AbstractRelyingPartyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldImplementsRelyingPartyInterface()
    {
        $rc = new \ReflectionClass('Fp\OpenIdBundle\RelyingParty\AbstractRelyingParty');

        $this->assertTrue($rc->implementsInterface('Fp\OpenIdBundle\RelyingParty\RelyingPartyInterface'));
    }

    /**
     * @test
     */
    public function shouldBeAbstractClass()
    {
        $rc = new \ReflectionClass('Fp\OpenIdBundle\RelyingParty\AbstractRelyingParty');

        $this->assertTrue($rc->isAbstract());
    }

    /**
     * @test
     */
    public function shouldNotSupportEmptyRequest()
    {
        $emptyRequest = Request::create('uri');

        $relyingParty = new AbstractRelyingPartyMock();

        $this->assertFalse($relyingParty->supports($emptyRequest));
    }

    /**
     * @test
     */
    public function couldBeConstructedWithoutAnyArguments()
    {
        new AbstractRelyingPartyMock();
    }

    /**
     * @test
     */
    public function shouldSupportIfQueryHasParameterStartsWithOpenId()
    {
        $request = Request::create('uri');
        $request->query->set('openid_parameter', 1);

        $relyingParty = new AbstractRelyingPartyMock();

        $this->assertTrue($relyingParty->supports($request));
    }

    /**
     * @test
     */
    public function shouldSupportIfRequestHasParameterStartsWithOpenId()
    {
        $request = Request::create('uri');
        $request->request->set('openid_parameter', 1);

        $relyingParty = new AbstractRelyingPartyMock();

        $this->assertTrue($relyingParty->supports($request));
    }

    /**
     * @test
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The relying party does not support the request
     */
    public function throwIfTryToManageNotSupportedRequest()
    {
        $notSupportedRequest = Request::create('uri');

        $relyingParty = new AbstractRelyingPartyMock();

        //guard
        $this->assertFalse($relyingParty->supports($notSupportedRequest));

        $relyingParty->manage($notSupportedRequest);
    }

    /**
     * @test
     */
    public function shouldCallVerifyAndReturnRedirectResponseIfOpenIdIdentifierParameterPresentInQuery()
    {
        $request = Request::create('uri');
        $request->query->set('openid_identifier', 'an_id');

        $relyingParty = new AbstractRelyingPartyMock();

        $result = $relyingParty->manage($request);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $result);
    }

    /**
     * @test
     */
    public function shouldCallVerifyAndReturnRedirectResponseIfOpenIdIdentifierParameterPresentInRequest()
    {
        $request = Request::create('uri');
        $request->request->set('openid_identifier', 'an_id');

        $relyingParty = new AbstractRelyingPartyMock();

        $result = $relyingParty->manage($request);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $result);
    }

    /**
     * @test
     */
    public function shouldCallCompleteAndReturnIdentityProviderResponseIfOpenIdIdentifierParameterNotPresent()
    {
        $request = Request::create('uri');
        $request->request->set('openid_parameter', 'param');

        $relyingParty = new AbstractRelyingPartyMock();

        $result = $relyingParty->manage($request);

        $this->assertInstanceOf('Fp\OpenIdBundle\RelyingParty\IdentityProviderResponse', $result);
    }

    /**
     * @test
     */
    public function shouldReturnEmptyRequiredAttributesIfRequestDoesNotHaveAny()
    {
        $request = Request::create('uri');

        $relyingParty = new AbstractRelyingPartyMock();

        $result = $relyingParty->callProtected('guessRequiredAttributes', array($request));

        $this->assertEquals(array(), $result);
    }

    /**
     * @test
     */
    public function shouldReturnExpectedRequiredAttributesIfRequestHaveSome()
    {
        $expectedAttributes = array('foo' => 'bar');

        $request = Request::create('uri');
        $request->attributes->set('required_attributes', $expectedAttributes);

        $relyingParty = new AbstractRelyingPartyMock();

        $result = $relyingParty->callProtected('guessRequiredAttributes', array($request));

        $this->assertEquals($expectedAttributes, $result);
    }

    /**
     * @test
     */
    public function shouldReturnEmptyOptionalAttributesIfRequestDoesNotHaveAny()
    {
        $request = Request::create('uri');

        $relyingParty = new AbstractRelyingPartyMock();

        $result = $relyingParty->callProtected('guessOptionalAttributes', array($request));

        $this->assertEquals(array(), $result);
    }

    /**
     * @test
     */
    public function shouldReturnExpectedOptionalAttributesIfRequestHaveSome()
    {
        $expectedAttributes = array('foo' => 'bar');

        $request = Request::create('uri');
        $request->attributes->set('optional_attributes', $expectedAttributes);

        $relyingParty = new AbstractRelyingPartyMock();

        $result = $relyingParty->callProtected('guessOptionalAttributes', array($request));

        $this->assertEquals($expectedAttributes, $result);
    }

    /**
     * @test
     */
    public function shouldReturnNullAsIdentifierIfRequestNotHaveOne()
    {
        $request = Request::create('uri');

        $relyingParty = new AbstractRelyingPartyMock();

        $result = $relyingParty->callProtected('guessIdentifier', array($request));

        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function shouldReturnExpectedIdentifierIfRequestHaveOne()
    {
        $expectedIdentifier = 'the_id';

        $request = Request::create('uri');
        $request->attributes->set('openid_identifier', $expectedIdentifier);

        $relyingParty = new AbstractRelyingPartyMock();

        $result = $relyingParty->callProtected('guessIdentifier', array($request));

        $this->assertEquals($expectedIdentifier, $result);
    }

    /**
     * @test
     */
    public function shouldReturnRequestHttpHostAsTrustRootIfRequestNotHaveAttributeTrustRoot()
    {
        $request = Request::create('uri');

        $relyingParty = new AbstractRelyingPartyMock();

        $result = $relyingParty->callProtected('guessTrustRoot', array($request));

        $this->assertEquals($request->getHttpHost(), $result);
    }

    /**
     * @test
     */
    public function shouldReturnExpectedTrustRootIfRequestHaveAttributeTrustRoot()
    {
        $expectedTrustRoot = 'the_trust_root';

        $request = Request::create('uri');
        $request->attributes->set('trust_root', $expectedTrustRoot);

        $relyingParty = new AbstractRelyingPartyMock();

        $result = $relyingParty->callProtected('guessTrustRoot', array($request));

        $this->assertEquals($expectedTrustRoot, $result);
    }

    /**
     * @test
     */
    public function shouldReturnRequestUriAsReturnUrl()
    {
        $request = Request::create('uri');

        $relyingParty = new AbstractRelyingPartyMock();

        $result = $relyingParty->callProtected('guessReturnUrl', array($request));

        $this->assertEquals($request->getUri(), $result);
    }
}

class AbstractRelyingPartyMock extends AbstractRelyingParty
{
    public $providerUrl = 'http://example.com/openid-provider';

    public $identity = 'an_id';

    public $attributes = array();

    protected function verify(Request $request)
    {
        return new RedirectResponse($this->providerUrl);
    }

    protected function complete(Request $request)
    {
        return new IdentityProviderResponse(
            $this->identity,
            $this->attributes
        );
    }

    public function callProtected($method, array $args = array())
    {
        return call_user_func_array(array($this, $method), $args);
    }
}