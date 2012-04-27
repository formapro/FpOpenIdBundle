<?php
namespace Fp\OpenIdBundle\Tests\Functional;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Kotlyar Maksim <kotlyar.maksim@gmail.com>
 * @since 4/27/12
 */
class TestController extends ContainerAware
{
    public function securedAction()
    {
        return new Response('Secured Content');
    }
}
