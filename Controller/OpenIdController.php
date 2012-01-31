<?php
namespace Fp\OpenIdBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;

class OpenIdController extends ContainerAware
{
    public function simpleFormAction()
    {
        $consumer = $this->container->get('fp_openid.consumer.provider')->provide(null);
        $templating = $this->container->get('templating');
        $view = $this->container->getParameter('openid.view.login_form');

        return $templating->renderResponse(
            $view,
            array(
                'identifier_configured' => $consumer->hasConfiguredIdentifier()
            )
        );
    }
}