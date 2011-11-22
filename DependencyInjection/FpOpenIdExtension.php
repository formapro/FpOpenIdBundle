<?php

namespace Fp\OpenIdBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Reference;

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

        $container->setParameter('fp_openid.security.authentication.provider.parameters', $configs['provider']);

        foreach ($configs['consumers'] as $name => $parameters) {
            // if only one consumer configured use it as default.
            if (1 == count($configs['consumers'])) {
                $configs['consumers']['default'] = true;
            }

            // try to guess a consumer service key
            if ($container->hasDefinition($name)) {
                $serviceKey = $name;
            } else if ($container->hasDefinition("fp_openid.consumer.{$name}")) {
                $serviceKey = "fp_openid.consumer.{$name}";
            } else {
                throw new \InvalidArgumentException(sprintf(
                    'Cannot find a consumer service definition for a given configuration option %s, tried next services: %s, %s',
                    $name,
                    $name,
                    "fp_openid.consumer.{$name}"
                ));
            }

            $consumerDefinition = $container->getDefinition($serviceKey);
            $consumerArguments = $consumerDefinition->getArguments();
            $consumerArguments[0] = $parameters;
            $consumerDefinition->setArguments($consumerArguments);

            // dynamically set trust root from a request
            if ('from_request' == $parameters['trust_root']) {
                $trustRootListenerDefinition = $container->getDefinition('fp_openid.trust_root_listener');
                $trustRootListenerDefinition->addMethodCall('addConsumer', array(new Reference($serviceKey)));
            }

            // add consumers to provider
            $consumerProviderDefinition = $container->getDefinition('fp_openid.consumer.provider');
            if ($parameters['default']) {
                $consumerProviderDefinition->addMethodCall(
                    'setDefault',
                    array(new Reference($serviceKey))
                );
            } else {
                $consumerProviderDefinition->addMethodCall(
                    'addConsumer',
                    array(new Reference($serviceKey))
                );
            }
        }
    }
}