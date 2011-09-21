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

        $container->setParameter('fp_openid.security.authentication.provider.parameters', array(
            'roles' => $configs['provider']['roles'],
            'return_route' => $configs['provider']['return_route'],
            'approve_route' => $configs['provider']['approve_route'],
        ));

        $container->setParameter('fp_openid.consumer.light_open_id.parameters', array(
            'trust_root' => $configs['light_open_id']['trust_root'],
            'required' => $configs['provider']['options_required'],
            'optional' => $configs['provider']['options_optional'],
        ));
    }
}