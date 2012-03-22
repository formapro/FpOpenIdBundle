<?php
namespace Fp\OpenIdBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Fp\OpenIdBundle\DependencyInjection\Security\Factory\OpenIdFactory;

class FpOpenIdBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new OpenIdFactory);
    }
}
