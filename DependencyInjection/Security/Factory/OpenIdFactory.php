<?php
namespace Fp\OpenIdBundle\DependencyInjection\Security\Factory;

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
        return 'pre_auth';
    }

    /**
     * {@inheritDoc}
     */
    public function getKey()
    {
        return 'openid';
    }

    /**
     * {@inheritDoc}
     */
    protected function getListenerId()
    {
        return 'security.authentication.listener.openid';
    }

    /**
     *
     * {@inheritDoc}
     */
	protected function createAuthProvider(ContainerBuilder $container, $id, $config, $userProviderId)
    {
        $providerId = 'security.authentication.provider.openid.'.$id;
        $container
            ->setDefinition($providerId, new DefinitionDecorator('security.authentication.provider.openid'))
            ->replaceArgument(3, new Reference($userProviderId))
        ;

        return $providerId;
	}
}