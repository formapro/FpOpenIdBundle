<?php
namespace Fp\OpenIdBundle\DependencyInjection\Security\Factory;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AbstractFactory;

class OpenIdFactory extends AbstractFactory
{
    /**
     *
     * {@inheritDoc}
     */
    public function getPosition()
    {
        return 'form';
    }

    /**
     * 
     * {@inheritDoc}
     */
    public function getKey()
    {
        return 'openid';
    }

    /**
     *
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
		return 'security.authentication.provider.openid';
	}

    /**
     *
     * {@inheritDoc}
     */
	protected function createEntryPoint($container, $id, $config, $defaultEntryPoint)
    {
        return $defaultEntryPoint;
        $entryPointId = 'security.authentication.openid_entry_point'.$id;
        
        $container
            ->setDefinition($entryPointId, new DefinitionDecorator('security.authentication.openid_entry_point'))
			->addArgument($config['login_path']);

        return $entryPointId;
    }
}