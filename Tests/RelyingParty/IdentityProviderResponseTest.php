<?php
namespace Fp\OpenIdBundle\Tests\RelyingParty;

use Fp\OpenIdBundle\RelyingParty\IdentityProviderResponse;

/**
 * @author Kotlyar Maksim <kotlyar.maksim@gmail.com>
 * @since 4/12/12
 */
class IdentityProviderResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function couldImplementSerialazibleInterface()
    {
        $rc = new \ReflectionClass('Fp\OpenIdBundle\RelyingParty\IdentityProviderResponse');

        $this->assertTrue($rc->implementsInterface('Serializable'));
    }

    /**
     * @test
     */
    public function couldBeConstructedWithIdentity()
    {
        new IdentityProviderResponse('an_identity');
    }

    /**
     * @test
     */
    public function couldBeConstructedWithIdentityAndAttributes()
    {
        new IdentityProviderResponse('an_identity', array('foo' => 'bar'));
    }

    /**
     * @test
     */
    public function shouldAllowGetIdentitySetInConstructor()
    {
        $expectedIdentity = 'the_identity';

        $response = new IdentityProviderResponse($expectedIdentity);

        $this->assertEquals($expectedIdentity, $response->getIdentity());
    }

    /**
     * @test
     */
    public function shouldGetEmptyArrayIfAttributesNotSetInConstructor()
    {
        $response = new IdentityProviderResponse('an_identity');

        $this->assertEquals(array(), $response->getAttributes());
    }

    /**
     * @test
     */
    public function shouldAllowGetAttributesSetInConstructor()
    {
        $expectedAttributes = array('foo' => 'bar');

        $response = new IdentityProviderResponse('an_identity', $expectedAttributes);

        $this->assertEquals($expectedAttributes, $response->getAttributes());
    }

    /**
     * @test
     */
    public function shouldUnserializeIdentity()
    {
        $expectedIdentity = 'the_identity';

        $response = unserialize(serialize(new IdentityProviderResponse($expectedIdentity)));

        $this->assertEquals($expectedIdentity, $response->getIdentity());
    }

    /**
     * @test
     */
    public function shouldUnserializeAttributes()
    {
        $expectedAttributes = array('foo' => 'bar');

        $response = unserialize(serialize(new IdentityProviderResponse('an_identity', $expectedAttributes)));

        $this->assertEquals($expectedAttributes, $response->getAttributes());
    }
}
