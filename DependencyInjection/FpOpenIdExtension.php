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
        $loader->load('services.xml');

        if ($dbDriver = $configs['db_driver']) {
            $identityClass = $configs['identity_class'];
            if (false == $identityClass) {
                throw new \InvalidArgumentException(sprintf(
                    'The option `%s` has to be configured to use db_driver',
                    'identity_class'
                ));
            }

            $dbDriver = strtolower($dbDriver);
            $supportedDbDrivers = array('orm');
            if (false == in_array($dbDriver, $supportedDbDrivers)) {
                throw new \InvalidArgumentException(sprintf(
                    'Invalid db driver "%s". Supported: %s',
                    $configs['db_driver'],
                    implode(', ', $supportedDbDrivers)
                ));
            }

            $loader->load(sprintf('%s.xml', $dbDriver));

            $container->setParameter('fp_openid.model.identity.class', $identityClass);
        }
    }
}