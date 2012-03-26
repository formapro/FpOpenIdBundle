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
        if (false == empty($this->options['required_parameters'])) {
            $request->attributes->set('openid_required_parameters', $this->options['required_parameters']);
        }
        if (false == empty($this->options['openid_optional_parameters'])) {
            $request->attributes->set('openid_optional_parameters', $this->options['required_parameters']);
        }

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
