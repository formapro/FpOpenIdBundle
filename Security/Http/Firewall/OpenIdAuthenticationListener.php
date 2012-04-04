<?php
namespace Fp\OpenIdBundle\Security\Http\Firewall;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationServiceException;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Fp\OpenIdBundle\RelyingParty\IdentityProviderResponse;
use Fp\OpenIdBundle\Security\Core\Authentication\Token\OpenIdToken;
use Fp\OpenIdBundle\Security\Http\Event\IdentityProvidedEvent;
use Fp\OpenIdBundle\Security\Http\SecurityEvents;

class OpenIdAuthenticationListener extends AbstractOpenIdAuthenticationListener
{
    /**
     * {@inheritdoc}
     */
    protected function attemptAuthentication(Request $request)
    {
        $openIdRequest = $request->duplicate();
        if (false == empty($this->options['required_parameters'])) {
            $openIdRequest->attributes->set('openid_required_parameters', $this->options['required_parameters']);
        }
        if (false == empty($this->options['openid_optional_parameters'])) {
            $openIdRequest->attributes->set('openid_optional_parameters', $this->options['required_parameters']);
        }

        $result = $this->getRelyingParty()->manage($openIdRequest);

        if ($result instanceof RedirectResponse) {
            return $result;
        }

        if ($result instanceof IdentityProviderResponse) {
            if ($this->getDispatcher()) {
                $identityProvidedEvent = new IdentityProvidedEvent($result->getIdentity(), $result->getAttributes(), $request);
                $this->getDispatcher()->dispatch(SecurityEvents::IDENTITY_PROVIDED, $identityProvidedEvent);

                if ($identityProvidedEvent->getResponse()) {
                    return $identityProvidedEvent->getResponse();
                }
            }

            return $this->authenticationManager->authenticate(new OpenIdToken($result->getIdentity(), $result->getAttributes()));
        }

        throw new \RuntimeException(sprintf(
            'The relying party %s::manage() must either return a RedirectResponse or instance of IdentityProviderResponse.',
            get_class($this->getRelyingParty())
        ));
    }
}
