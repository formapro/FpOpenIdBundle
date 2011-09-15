<?php
namespace Fp\OpenIdBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;

class OpenIdController extends ContainerAware
{
    public function simpleFormAction()
    {
        $templating = $this->container->get('templating');
        $view = $this->container->getParameter('openid.view.simple_form');

        return $templating->renderResponse($view, array());
    }
}