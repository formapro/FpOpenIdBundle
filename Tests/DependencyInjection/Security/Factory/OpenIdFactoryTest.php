<?php
namespace Fp\OpenIdBundle\Tests\DependencyInjection\Security\Factory;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

use Fp\OpenIdBundle\DependencyInjection\Security\Factory\OpenIdFactory;

class OpenIdFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function couldBeConstructedWithoutAnyArguments()
    {
        new OpenIdFactory();
    }

    /**
     * @test
     */
    public function shouldAllowGetKey()
    {
        $factory = new OpenIdFactory();

        $this->assertEquals('fp_openid', $factory->getKey());
    }

    /**
     * @test
     */
    public function shouldAllowGetPosition()
    {
        $factory = new OpenIdFactory();

        $this->assertEquals('form', $factory->getPosition());
    }

    /**
     * @test
     */
    public function shouldAddCreateIfNotExistToConfigurationWithDefaultFalse()
    {
        $factory = new OpenIdFactory();

        $treeBuilder = new TreeBuilder();

        $factory->addConfiguration($treeBuilder->root('name'));

        $childeren = $treeBuilder->buildTree()->getChildren();

        $this->assertArrayHasKey('create_user_if_not_exists', $childeren);

        $this->assertInstanceOf('Symfony\Component\Config\Definition\BooleanNode', $childeren['create_user_if_not_exists']);
        $this->assertFalse($childeren['create_user_if_not_exists']->getDefaultValue());
    }

    /**
     * @test
     */
    public function shouldAddRequiredAttributesToConfigurationWithDefaultEmptyArray()
    {
        $factory = new OpenIdFactory();

        $treeBuilder = new TreeBuilder();

        $factory->addConfiguration($treeBuilder->root('name'));

        $childeren = $treeBuilder->buildTree()->getChildren();

        $this->assertArrayHasKey('required_attributes', $childeren);

        $this->assertInstanceOf('Symfony\Component\Config\Definition\PrototypedArrayNode', $childeren['required_attributes']);
        $this->assertEquals(array(), $childeren['required_attributes']->getDefaultValue());
    }

    /**
     * @test
     */
    public function shouldAddOptionalAttributesToConfigurationWithDefaultEmptyArray()
    {
        $factory = new OpenIdFactory();

        $treeBuilder = new TreeBuilder();

        $factory->addConfiguration($treeBuilder->root('name'));

        $childeren = $treeBuilder->buildTree()->getChildren();

        $this->assertArrayHasKey('optional_attributes', $childeren);

        $this->assertInstanceOf('Symfony\Component\Config\Definition\PrototypedArrayNode', $childeren['optional_attributes']);
        $this->assertEquals(array(), $childeren['optional_attributes']->getDefaultValue());
    }

    /**
     * @test
     */
    public function shouldAddRelyingPartyToConfigurationWithDefaultRelyingPartyServiceId()
    {
        $factory = new OpenIdFactory();

        $treeBuilder = new TreeBuilder();

        $factory->addConfiguration($treeBuilder->root('name'));

        $childeren = $treeBuilder->buildTree()->getChildren();

        $this->assertArrayHasKey('relying_party', $childeren);

        $this->assertInstanceOf('Symfony\Component\Config\Definition\ScalarNode', $childeren['relying_party']);
        $this->assertEquals('fp_openid.relying_party.default', $childeren['relying_party']->getDefaultValue());
    }

    /**
     * @test
     */
    public function shouldReturnArrayWhichContainsProviderListenerAndEntryPointIds()
    {
        $containerBuilder = new ContainerBuilder(new ParameterBag());

        $config = array(
            //'provider' => 'user.provider.id',
            'csrf_provider' => 'form.csrf_provider',
            'remember_me' => true,
            'check_path' => '/login_check',
            'login_path' => '/login',
            'use_forward' => false,
            'always_use_default_target_path' => false,
            'default_target_path' => '/',
            'target_path_parameter' => '_target_path',
            'use_referer' => false,
            'failure_path' => null,
            'failure_forward' => false,
            'username_parameter' => '_username',
            'password_parameter' => '_password',
            'csrf_parameter' => '_csrf_token',
            'intention' => 'authenticate',
            'post_only' => true,
        );

        $factory = new OpenIdFactory();

        $result = $factory->create($containerBuilder, 'main', $config, 'user.provider.id', $defaultEntryPoint = null);

        $this->assertInternalType('array', $result);
        $this->assertCount(3, $result);
        $this->assertContainsOnly('string', $result);
    }

    /**
     * @test
     */
    public function shouldReturnFpOpenIdProviderWithPostfixId()
    {
        $containerBuilder = new ContainerBuilder(new ParameterBag());

        $config = array(
            'remember_me' => true,
            'login_path' => '/login',
            'use_forward' => false,
        );

        $factory = new OpenIdFactory();

        list($providerId) = $factory->create($containerBuilder, 'main', $config, 'user.provider.id', $defaultEntryPoint = null);

        $this->assertStringStartsWith('security.authentication.provider.fp_openid', $providerId);
        $this->assertStringEndsWith('.main', $providerId);
    }

    /**
     * @test
     */
    public function shouldReturnFpOpenIdFirewallListenerWithPostfixId()
    {
        $containerBuilder = new ContainerBuilder(new ParameterBag());

        $config = array(
            'remember_me' => true,
            'login_path' => '/login',
            'use_forward' => false,
        );

        $factory = new OpenIdFactory();

        list(,$listenerId) = $factory->create($containerBuilder, 'main', $config, 'user.provider.id', $defaultEntryPoint = null);

        $this->assertStringStartsWith('security.authentication.listener.fp_openid', $listenerId);
        $this->assertStringEndsWith('.main', $listenerId);
    }

    /**
     * @test
     */
    public function shouldReturnFormEntryPointyWithPostfixId()
    {
        $containerBuilder = new ContainerBuilder(new ParameterBag());

        $config = array(
            'remember_me' => true,
            'login_path' => '/login',
            'use_forward' => false,
        );

        $factory = new OpenIdFactory();

        list(,, $entryPointId) = $factory->create($containerBuilder, 'main', $config, 'user.provider.id', $defaultEntryPoint = null);

        $this->assertStringStartsWith('security.authentication.form_entry_point', $entryPointId);
        $this->assertStringEndsWith('.main', $entryPointId);
    }

    /**
     * @test
     */
    public function shouldInjectRelyingPartyViaListenerSetterMethod()
    {
        $expectedRelyingPartyId = 'custom.relying_party.id';

        $containerBuilder = new ContainerBuilder(new ParameterBag());

        $config = array(
            'remember_me' => true,
            'login_path' => '/login',
            'use_forward' => false,
            'relying_party' => $expectedRelyingPartyId
        );

        $factory = new OpenIdFactory();

        list(, $listenerId) = $factory->create($containerBuilder, 'main', $config, 'user.provider.id', $defaultEntryPoint = null);

        $this->assertTrue($containerBuilder->hasDefinition($listenerId));

        $listenerDefinition = $containerBuilder->getDefinition($listenerId);

        $methodCalls = $listenerDefinition->getMethodCalls();
        $this->assertCount(1, $methodCalls);
        $this->assertEquals('setRelyingParty', $methodCalls[0][0]);

        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $methodCalls[0][1][0]);
        $this->assertEquals($expectedRelyingPartyId, (string) $methodCalls[0][1][0]);
    }

    /**
     * @test
     */
    public function shouldNotAddAnyArgumentsToAuthenticationProviderIfProviderNotSetInConfig()
    {
        $containerBuilder = new ContainerBuilder(new ParameterBag());

        $config = array(
            'remember_me' => true,
            'login_path' => '/login',
            'use_forward' => false,
        );

        $factory = new OpenIdFactory();

        list($providerId) = $factory->create($containerBuilder, 'main', $config, 'user.provider.id', $defaultEntryPoint = null);

        $this->assertTrue($containerBuilder->hasDefinition($providerId));

        $providerDefinition = $containerBuilder->getDefinition($providerId);

        $arguments = $providerDefinition->getArguments();
        $this->assertEquals(array(), $arguments);
    }

    /**
     * @test
     */
    public function shouldAddUserManagerUserCheckerAndWhetherCreateUserOrNotArgumentsToAuthenticationProviderIfProviderSetInConfig()
    {
        $expectedUserProviderId = 'user.provider.id';
        $expectedCreateUserIfNotExists = true;

        $containerBuilder = new ContainerBuilder(new ParameterBag());

        $config = array(
            'remember_me' => true,
            'login_path' => '/login',
            'use_forward' => false,
            'provider' => $expectedUserProviderId,
            'create_user_if_not_exists' => $expectedCreateUserIfNotExists,
        );

        $factory = new OpenIdFactory();

        list($providerId) = $factory->create($containerBuilder, 'main', $config, 'user.provider.id', $defaultEntryPoint = null);

        $this->assertTrue($containerBuilder->hasDefinition($providerId));

        $providerDefinition = $containerBuilder->getDefinition($providerId);

        $arguments = $providerDefinition->getArguments();

        $this->assertCount(3, $arguments);

        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $arguments[0]);
        $this->assertEquals($expectedUserProviderId, (string) $arguments[0]);

        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $arguments[1]);
        $this->assertEquals('security.user_checker', (string) $arguments[1]);

        $this->assertEquals($expectedCreateUserIfNotExists, $arguments[2]);
    }
}