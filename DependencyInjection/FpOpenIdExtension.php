<?php

namespace Fp\OpenIdBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class FpOpenIdExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configs = $this->processConfiguration(new Configuration(), $configs);
        
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('openid.xml');

        $container->setParameter('openid.light_open_id.trust_root', $configs['light_open_id']['trust_root']);
        
        $container->setParameter('security.authentication.provider.openid.parameters', array(
            'routerService' => $configs['provider']['router_service'],
            'lightOpenIdService' => $configs['provider']['light_open_id_service'],
            'tokenPersisterService' => $configs['provider']['token_persister_service'],
            'roles' => $configs['provider']['roles'],
            'return_route' => $configs['provider']['return_route'],
            'approve_route' => $configs['provider']['approve_route'],
            'openid_required_options' => $configs['provider']['options_required'],
            'openid_optional_options' => $configs['provider']['options_optional'],
        ));
    }
}