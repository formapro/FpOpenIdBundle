<?php
namespace Fp\OpenIdBundle\Security\Http\Firewall;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationServiceException;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Fp\OpenIdBundle\Security\Core\Authentication\Token\OpenIdToken;

class OpenIdAuthenticationListener extends AbstractOpenIdAuthenticationListener
{
    /**
     * {@inheritdoc}
     */
    protected function attemptAuthentication(Request $request)
    {
        $result = $this->getClient()->manage($request);

        if ($result instanceof RedirectResponse) {
            return $result;
        }

        if ($result instanceof OpenIdToken) {
            return $this->authenticationManager->authenticate($result);
        }

        throw new \RuntimeException(sprintf(
            'The client %s::manage() must either return a RedirectResponse or instance of OpenIdTokenToken.',
            get_class($this->getClient())
        ));
    }
}
