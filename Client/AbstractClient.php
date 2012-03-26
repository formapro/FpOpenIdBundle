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

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return string
     */
    protected function guessReturnUrl(Request $request)
    {
        return $request->getUri();
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return string
     */
    protected function guessIdentifier(Request $request)
    {
        return $request->get('openid_identifier');
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return string
     */
    protected function guessRequiredParameters(Request $request)
    {
        return $request->get('openid_required_parameters', array());
    }

    /**
    * @param \Symfony\Component\HttpFoundation\Request $request
    *
    * @return string
    */
    protected function guessOptionalParameters(Request $request)
    {
        return $request->get('openid_optional_parameters', array());
    }

    /**
     * @param string $identity
     * @param array $attributes
     */
    protected function guessUser($identity, array $attributes = array())
    {
        $attributes = array_merge(array(
            'contact/email' => null,
            'namePerson/first' => null,
            'namePerson/last' => null,
        ), $attributes);

        $username = '';
        if ($attributes['contact/email']) {
            $username = $attributes['contact/email'];
        } else if ($attributes['namePerson/first']) {
            $username = $attributes['namePerson/first'];

            if ($attributes['namePerson/last']) {
                $username .= " {$attributes['namePerson/last']}";
            }
        }

        $provider = parse_url($identity, PHP_URL_HOST);

        return $username . ' by ' . $provider;
    }
}