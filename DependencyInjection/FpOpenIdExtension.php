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
        $loader->load('services.xml');

        $container->setParameter('fp_openid.template.engine', $configs['template']['engine']);

        if ($configs['db_driver']) {
            $this->loadDbDriver($configs, $container, $loader);
        }
    }

    /**
     * @param array $configs
     * @param ContainerBuilder $container
     *
     * @throws \InvalidArgumentException
     */
    protected function loadDbDriver(array $configs, ContainerBuilder $container, $loader)
    {
        $dbDriver = strtolower($configs['db_driver']);
        $supportedDbDrivers = array('orm', 'mongodb');

        if (false == in_array($dbDriver, $supportedDbDrivers)) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid db driver "%s". Supported: %s',
                $configs['db_driver'],
                implode(', ', $supportedDbDrivers)
            ));
        }

        $identityClass = $configs['identity_class'];
        if (false == $identityClass) {
            throw new \InvalidArgumentException(sprintf(
                'The option `%s` has to be configured to use db_driver',
                'identity_class'
            ));
        }

        if (false == class_exists($identityClass, true)) {
            throw new \InvalidArgumentException(sprintf(
                'The option `%s` contains %s but it is not a valid class name.',
                'identity_class',
                $identityClass
            ));
        }

        $loader->load(sprintf('%s.xml', $dbDriver));

        $container->setParameter('fp_openid.model.identity.class', $identityClass);
    }
}
