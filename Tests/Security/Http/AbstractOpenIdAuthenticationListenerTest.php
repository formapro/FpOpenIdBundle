<?php
namespace Fp\OpenIdBundle\Tests\Security\Http\Firewall;

class AbstractOpenIdAuthenticationListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function couldBeConstructedWithRequiredSetOfArguments()
    {
        $constructorArguments = array(
            $this->createSecurityContextMock(),
            $this->createAuthenticationManagerMock(),
            $this->createSessionAuthenticationStrategyMock(),
            $this->createHttpUtilsMock(),
            'providerKey',
            $this->createAuthenticationSuccessHandlerMock(),
            $this->createAuthenticationFailureHandlerMock(),
        );

        $this->getMockForAbstractClass(
            'Fp\OpenIdBundle\Security\Http\Firewall\AbstractOpenIdAuthenticationListener',
            $constructorArguments
        );
    }

    /**
     * @test
     */
    public function shouldSetEmptyArrayAsRequiredAttributesOptionsInConstructor()
    {
        $constructorArguments = array(
            $this->createSecurityContextMock(),
            $this->createAuthenticationManagerMock(),
            $this->createSessionAuthenticationStrategyMock(),
            $this->createHttpUtilsMock(),
            'providerKey',
            $this->createAuthenticationSuccessHandlerMock(),
            $this->createAuthenticationFailureHandlerMock(),
        );

        $listener = $this->getMockForAbstractClass(
            'Fp\OpenIdBundle\Security\Http\Firewall\AbstractOpenIdAuthenticationListener',
            $constructorArguments
        );

        $options = $this->readAttribute($listener, 'options');

        $this->assertArrayHasKey('required_attributes', $options);
        $this->assertEquals(array(), $options['required_attributes']);
    }

    /**
     * @test
     */
    public function shouldSetCustomRequiredAttributesToOptionsInConstructor()
    {
        $expectedRequiredAttributes = array(
            'foo' => 'foo',
            'bar' => 'bar'
        );

        $constructorArguments = array(
            $this->createSecurityContextMock(),
            $this->createAuthenticationManagerMock(),
            $this->createSessionAuthenticationStrategyMock(),
            $this->createHttpUtilsMock(),
            'providerKey',
            $this->createAuthenticationSuccessHandlerMock(),
            $this->createAuthenticationFailureHandlerMock(),
            $options = array(
                'required_attributes' => $expectedRequiredAttributes
            )
        );

        $listener = $this->getMockForAbstractClass(
            'Fp\OpenIdBundle\Security\Http\Firewall\AbstractOpenIdAuthenticationListener',
            $constructorArguments
        );

        $options = $this->readAttribute($listener, 'options');

        $this->assertArrayHasKey('required_attributes', $options);
        $this->assertEquals($expectedRequiredAttributes, $options['required_attributes']);
    }

    /**
     * @test
     */
    public function shouldSetEmptyArrayAsOptionalAttributesOptionsInConstructor()
    {
        $constructorArguments = array(
            $this->createSecurityContextMock(),
            $this->createAuthenticationManagerMock(),
            $this->createSessionAuthenticationStrategyMock(),
            $this->createHttpUtilsMock(),
            'providerKey',
            $this->createAuthenticationSuccessHandlerMock(),
            $this->createAuthenticationFailureHandlerMock(),
            $options = array()
        );

        $listener = $this->getMockForAbstractClass(
            'Fp\OpenIdBundle\Security\Http\Firewall\AbstractOpenIdAuthenticationListener',
            $constructorArguments
        );

        $options = $this->readAttribute($listener, 'options');

        $this->assertArrayHasKey('required_attributes', $options);
        $this->assertEquals(array(), $options['required_attributes']);
    }

    /**
     * @test
     */
    public function shouldAddOptionalAttributesToOptionsWithEmptyArrayAsDefaultValue()
    {
        $constructorArguments = array(
            $this->createSecurityContextMock(),
            $this->createAuthenticationManagerMock(),
            $this->createSessionAuthenticationStrategyMock(),
            $this->createHttpUtilsMock(),
            'providerKey',
            $this->createAuthenticationSuccessHandlerMock(),
            $this->createAuthenticationFailureHandlerMock(),
            $options = array()
        );

        $listener = $this->getMockForAbstractClass(
            'Fp\OpenIdBundle\Security\Http\Firewall\AbstractOpenIdAuthenticationListener',
            $constructorArguments
        );

        $options = $this->readAttribute($listener, 'options');

        $this->assertArrayHasKey('optional_attributes', $options);
        $this->assertEquals(array(), $options['optional_attributes']);
    }

    /**
     * @test
     */
    public function shouldSetCustomOptionalAttributesToOptionsInConstructor()
    {
        $expectedOptionalAttributes = array(
            'foo' => 'foo',
            'bar' => 'bar'
        );

        $constructorArguments = array(
            $this->createSecurityContextMock(),
            $this->createAuthenticationManagerMock(),
            $this->createSessionAuthenticationStrategyMock(),
            $this->createHttpUtilsMock(),
            'providerKey',
            $this->createAuthenticationSuccessHandlerMock(),
            $this->createAuthenticationFailureHandlerMock(),
            $options = array(
                'optional_attributes' => $expectedOptionalAttributes
            )
        );

        $listener = $this->getMockForAbstractClass(
            'Fp\OpenIdBundle\Security\Http\Firewall\AbstractOpenIdAuthenticationListener',
            $constructorArguments
        );

        $options = $this->readAttribute($listener, 'options');

        $this->assertArrayHasKey('optional_attributes', $options);
        $this->assertEquals($expectedOptionalAttributes, $options['optional_attributes']);
    }

    /**
     * @test
     */
    public function shouldAllowToGetRelyingPartySetPreviously()
    {
        $expectedRelyingParty = $this->createRelyingPartyMock();

        $listener = $this->getMockForAbstractClass(
            'Fp\OpenIdBundle\Security\Http\Firewall\AbstractOpenIdAuthenticationListener',
            array(),
            '',
            false
        );

        $listener->setRelyingParty($expectedRelyingParty);

        $ro = new \ReflectionObject($listener);
        $rm = $ro->getMethod('getRelyingParty');
        $rm->setAccessible(true);
        $actualRelyingParty = $rm->invoke($listener);
        $rm->setAccessible(false);

        $this->assertSame($expectedRelyingParty, $actualRelyingParty);
    }

    /**
     * @test
     *
     * @expectedException RuntimeException
     * @expectedExceptionMessage The relying party is required for the listener work, but it was not set. Seems like miss configuration
     */
    public function throwIfGettingRelyingPartyWithoutSetItPreviously()
    {
        $listener = $this->getMockForAbstractClass(
            'Fp\OpenIdBundle\Security\Http\Firewall\AbstractOpenIdAuthenticationListener',
            array(),
            '',
            false
        );

        $ro = new \ReflectionObject($listener);
        $rm = $ro->getMethod('getRelyingParty');
        $rm->setAccessible(true);
        $actualRelyingParty = $rm->invoke($listener);
    }

    /**
     * @test
     */
    public function shouldAllowSetRelyingParty()
    {
        $listener = $this->getMockForAbstractClass(
            'Fp\OpenIdBundle\Security\Http\Firewall\AbstractOpenIdAuthenticationListener',
            array(),
            '',
            false
        );

        $listener->setRelyingParty($this->createRelyingPartyMock());
    }

    protected function createSecurityContextMock()
    {
        return $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
    }

    protected function createAuthenticationManagerMock()
    {
        return $this->getMock('Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface');
    }

    protected function createSessionAuthenticationStrategyMock()
    {
        return $this->getMock('Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface');
    }

    protected function createHttpUtilsMock()
    {
        return $this->getMock('Symfony\Component\Security\Http\HttpUtils');
    }

    protected function createRelyingPartyMock()
    {
        return $this->getMock('Fp\OpenIdBundle\RelyingParty\RelyingPartyInterface');
    }
    
    protected function createAuthenticationSuccessHandlerMock()
    {
        return $this->getMock('Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface');
    }
    
    protected function createAuthenticationFailureHandlerMock()
    {
        return $this->getMock('Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface');
    }
}