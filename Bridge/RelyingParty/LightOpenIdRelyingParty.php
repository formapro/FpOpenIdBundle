<?php
namespace Fp\OpenIdBundle\Bridge\RelyingParty;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Fp\OpenIdBundle\RelyingParty\AbstractRelyingParty;
use Fp\OpenIdBundle\RelyingParty\IdentityProviderResponse;
use Fp\OpenIdBundle\RelyingParty\Exception\OpenIdAuthenticationCanceledException;
use Fp\OpenIdBundle\RelyingParty\Exception\OpenIdAuthenticationValidationFailedException;

class LightOpenIdRelyingParty extends AbstractRelyingParty
{
    /**
     * {@inheritdoc}
     */
    protected function verify(Request $request)
    {
        $lightOpenId = $this->createLightOpenID($this->guessTrustRoot($request));

        $lightOpenId->identity = $this->guessIdentifier($request);
        $lightOpenId->returnUrl = $this->guessReturnUrl($request);
        $lightOpenId->required = $this->guessRequiredAttributes($request);
        $lightOpenId->optional = $this->guessOptionalAttributes($request);

        return new RedirectResponse($lightOpenId->authUrl());
    }

    /**
     * {@inheritdoc}
     */
    protected function complete(Request $request)
    {
        $lightOpenId = $this->createLightOpenID($this->guessTrustRoot($request));

        if (false == $lightOpenId->validate()) {
            if($lightOpenId->mode == 'cancel') {
              throw new OpenIdAuthenticationCanceledException('Authentication was canceled by the user on a provider side');
            }

            throw new OpenIdAuthenticationValidationFailedException(sprintf(
               "Validation of response parameters failed for request: \n\n%s",
               $request
            ));
        }

        return new IdentityProviderResponse($lightOpenId->identity, $lightOpenId->getAttributes());
    }

    /**
     * @param string $trustRoot
     *
     * @return \LightOpenID
     */
    protected function createLightOpenID($trustRoot)
    {
        return new \LightOpenID($trustRoot);
    }
}