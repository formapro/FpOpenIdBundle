<?php
namespace Fp\OpenIdBundle\DependencyInjection\Security\Factory;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AbstractFactory;

class OpenIdFactory extends AbstractFactory
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->addOption('create_user_if_not_exists', false);
    }

    /**
     * {@inheritDoc}
     */
    public function getPosition()
    {
        return 'form';
    }

    /**
     * {@inheritDoc}
     */
    public function getKey()
    {
        return 'fp_openid';
    }

    /**
     * {@inheritDoc}
     */
    public function addConfiguration(NodeDefinition $node)
    {
        parent::addConfiguration($node);

        // it isn't done in constructor because parent::addConfiguration cannot handle array default value
        $this->addOption('required_attributes', array());
        $this->addOption('optional_attributes', array());

        $node
            ->children()
                ->scalarNode('relying_party')->defaultValue('fp_openid.relying_party.default')->cannotBeEmpty()->end()
                ->arrayNode('roles')
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('required_attributes')
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('optional_attributes')
                    ->prototype('scalar')->end()
                ->end()
            ->end()
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function getListenerId()
    {
        return 'security.authentication.listener.fp_openid';
    }

    protected function createListener($container, $id, $config, $userProvider)
    {
        $listenerId = parent::createListener($container, $id, $config, $userProvider);

        if (isset($config['relying_party'])) {
            $container
                ->getDefinition($listenerId)
                ->addMethodCall('setRelyingParty', array(new Reference($config['relying_party'])))
            ;
        }

        return $listenerId;
    }

    /**
     * {@inheritDoc}
     */
	protected function createAuthProvider(ContainerBuilder $container, $id, $config, $userProviderId)
    {
        $providerId = 'security.authentication.provider.fp_openid.'.$id;
        $provider = new DefinitionDecorator('security.authentication.provider.fp_openid');
        $container->setDefinition($providerId, $provider);

        // with user provider
        if (isset($config['provider'])) {
            $provider
                ->addArgument(new Reference($userProviderId))
                ->addArgument(new Reference('security.user_checker'))
                ->addArgument($config['create_user_if_not_exists'])
            ;
        }

        return $providerId;
	}

    /**
     * {@inheritDoc}
     */
    protected function createEntryPoint($container, $id, $config, $defaultEntryPoint)
    {
        $entryPointId = 'security.authentication.form_entry_point.'.$id;

        $container
            ->setDefinition($entryPointId, new DefinitionDecorator('security.authentication.form_entry_point'))
            ->addArgument(new Reference('security.http_utils'))
            ->addArgument($config['login_path'])
            ->addArgument($config['use_forward'])
        ;

        return $entryPointId;
    }
}