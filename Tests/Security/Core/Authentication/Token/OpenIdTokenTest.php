<?php
namespace Fp\OpenIdBundle\Tests\Security\Core\Authentication\Token;

use Fp\OpenIdBundle\Security\Core\Authentication\Token\OpenIdToken;

/**
 * @author Kotlyar Maksim <kotlyar.maksim@gmail.com>
 * @since 4/26/12
 */
class OpenIdTokenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldBeSubClassOfAbstractToken()
    {
        $rc = new \ReflectionClass('Fp\OpenIdBundle\Security\Core\Authentication\Token\OpenIdToken');
        
        $this->assertTrue($rc->isSubclassOf('Symfony\Component\Security\Core\Authentication\Token\AbstractToken'));
    }
    
    /**
     * @test
     */
    public function couldBeConstructedWithProviderKeyAndIdentityAsArguments()
    {
        new OpenIdToken('provider_key', 'identity');
    }
    
    /**
     * @test
     */
    public function shouldAllowGetProviderKeySetInConstructor()
    {
        $expectedProviderKey = 'provider_key';
        
        $token = new OpenIdToken($expectedProviderKey, 'identity');
        
        $token->getProviderKey($expectedProviderKey, $token->getProviderKey());
    }

    /**
     * @test
     */
    public function shouldAllowGetIdentitySetInConstructor()
    {
        $expectedIdentity = 'the_identity';

        $token = new OpenIdToken('provider_key', $expectedIdentity);

        $token->getProviderKey($expectedIdentity, $token->getIdentity());
    }

    /**
     * @test
     */
    public function shouldUnserializeIdentity()
    {
        $expectedIdentity = 'the_identity';
        
        $token = new OpenIdToken('provider_key', $expectedIdentity);

        $unserializedToken = unserialize(serialize($token));

        $this->assertEquals($expectedIdentity, $unserializedToken->getIdentity());
    }

    /**
     * @test
     */
    public function shouldUnserializeProviderKey()
    {
        $expectedProviderKey = 'the_provider_key';

        $token = new OpenIdToken($expectedProviderKey, 'identity');

        $unserializedToken = unserialize(serialize($token));

        $this->assertEquals($expectedProviderKey, $unserializedToken->getProviderKey());
    }

    /**
     * @test
     */
    public function shouldUnserializeAttributes()
    {
        $expectedAttributes = array(
            'foo' => 'foo',
            'bar' => 'bar',
        );

        $token = new OpenIdToken('provider_key', 'identity');
        $token->setAttributes($expectedAttributes);

        $unserializedToken = unserialize(serialize($token));

        $this->assertEquals($expectedAttributes, $unserializedToken->getAttributes());
    }
}
