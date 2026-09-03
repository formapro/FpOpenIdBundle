<?php
namespace Fp\OpenIdBundle\RelyingParty;

use Fp\OpenIdBundle\RelyingParty\Exception\OpenIdAuthenticationCanceledException;
use Fp\OpenIdBundle\RelyingParty\Exception\OpenIdAuthenticationValidationFailedException;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
        
        if ($request->query->has('openid_mode') || $request->request->has('openid_mode')) {
            return $this->complete($request);
        }

        return $this->verify($request);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    abstract protected function verify(Request $request);

    /**
     * @param Request $request
     *
     * @return IdentityProviderResponse
     * @throws OpenIdAuthenticationCanceledException
     *
     * @throws OpenIdAuthenticationValidationFailedException
     */
    abstract protected function complete(Request $request);

    /**
     * @param Request $request
     *
     * @return string
     */
    protected function guessTrustRoot(Request $request)
    {
        return $request->attributes->get('trust_root', $request->getSchemeAndHttpHost());
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    protected function guessReturnUrl(Request $request)
    {
        return $request->getUri();
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    protected function guessIdentifier(Request $request)
    {
        return $request->get('openid_identifier');
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    protected function guessRequiredAttributes(Request $request)
    {
        return $request->get('required_attributes', array());
    }

    /**
    * @param Request $request
    *
    * @return string
    */
    protected function guessOptionalAttributes(Request $request)
    {
        return $request->get('optional_attributes', array());
    }
}
