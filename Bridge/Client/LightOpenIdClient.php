<?php
namespace Fp\OpenIdBundle\Bridge\Client;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Fp\OpenIdBundle\Client\AbstractClient;
use Fp\OpenIdBundle\Security\Core\Authentication\Token\OpenIdToken;
use Fp\OpenIdBundle\Security\Core\Exception\OpenIdAuthenticationCanceledException;
use Fp\OpenIdBundle\Security\Core\Exception\OpenIdAuthenticationValidationFailedException;

class LightOpenIdClient extends AbstractClient
{
    /**
     * {@inheritdoc}
     */
    protected function verify(Request $request)
    {
        $lightOpenId = $this->createLightOpenID($this->guessTrustRoot($request));

        $lightOpenId->identity = $this->guessIdentifier($request);
        $lightOpenId->returnUrl = $this->guessReturnUrl($request);
        $lightOpenId->required = $this->guessRequiredParameters($request);
        $lightOpenId->optional = $this->guessOptionalParameters($request);

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

        $token = new OpenIdToken($lightOpenId->identity);
        $token->setAttributes($lightOpenId->getAttributes());

        return $token;
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