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

        $this->addOption('required_parameters', array());
        $this->addOption('optional_parameters', array());

        $node
            ->children()
                ->scalarNode('client')->defaultValue('fp_openid.client.default')->cannotBeEmpty()->end()
                ->arrayNode('required_parameters')
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('optional_parameters')
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

        if (isset($config['client'])) {
            $container
                ->getDefinition($listenerId)
                ->addMethodCall('setClient', array(new Reference($config['client'])))
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

        $container
            ->setDefinition($providerId, new DefinitionDecorator('security.authentication.provider.fp_openid'))
        ;

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