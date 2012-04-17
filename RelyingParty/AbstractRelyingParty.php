<?php
namespace Fp\OpenIdBundle\RelyingParty;

use Symfony\Component\HttpFoundation\Request;

abstract class AbstractRelyingParty implements RelyingPartyInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports(Request $request)
    {
        foreach ($request->query->all() as $name => $value) {
            if (0 === strpos($name, 'openid')) {
                return true;
            }
        }

        foreach ($request->request->all() as $name => $value) {
            if (0 === strpos($name, 'openid')) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function manage(Request $request)
    {
        if (false == $this->supports($request)) {
            throw new \InvalidArgumentException('The relying party does not support the request');
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
     *
     * @throws \Fp\OpenIdBundle\RelyingParty\Exception\OpenIdAuthenticationValidationFailedException
     * @throws \Fp\OpenIdBundle\RelyingParty\Exception\OpenIdAuthenticationCanceledException
     *
     * @return \Fp\OpenIdBundle\RelyingParty\IdentityProviderResponse
     */
    abstract protected function complete(Request $request);

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
}