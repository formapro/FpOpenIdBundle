<?php
namespace Fp\OpenIdBundle\Client;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ParameterBag;

abstract class AbstractClient implements ClientInterface
{
    /**
     * {@inheritdoc}
     */
    public function canManage(Request $request)
    {
        return count($this->findOpenIdParameters($request)) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function manage(Request $request)
    {
        if (false == $this->canManage($request)) {
            throw new \RuntimeException('The client can not manage the request');
        }

        if ($identifier = $request->get("openid_identifier", false)) {
            return $this->verify($request);
        }

        return $this->complete($request);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    abstract protected function verify(Request $request);

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $returnUrl
     *
     * @throws \Fp\OpenIdBundle\Security\Core\Exception\OpenIdAuthenticationValidationFailedException
     * @throws \Fp\OpenIdBundle\Security\Core\Exception\OpenIdAuthenticationCanceledException
     *
     * @return \Fp\OpenIdBundle\Security\Core\Authentication\Token\OpenIdToken
     */
    abstract protected function complete(Request $request);

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\ParameterBag
     */
    protected function findOpenIdParameters(Request $request)
    {
        $openIdParametersBag = new ParameterBag();
        foreach ($request->query->all() as $name => $value) {
            if (0 === strpos($name, 'openid')) {
                $openIdParametersBag->set($name, $value);
            }
        }

        foreach ($request->request->all() as $name => $value) {
            if (0 === strpos($name, 'openid')) {
                $openIdParametersBag->set($name, $value);
            }
        }

        return $openIdParametersBag;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return string
     */
    protected function guessTrustRoot(Request $request)
    {
        return $request->attributes->get('openid_trust_root', $request->getHttpHost());
    }
}