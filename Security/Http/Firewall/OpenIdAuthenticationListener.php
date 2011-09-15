<?php
namespace Fp\OpenIdBundle\Security\Http\Firewall;

use Symfony\Component\Security\Http\Firewall\AbstractAuthenticationListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

use Fp\OpenIdBundle\Security\Core\Authentication\Token\OpenIdToken;

class OpenIdAuthenticationListener extends AbstractAuthenticationListener
{
	protected function attemptAuthentication(Request $request)
    {
        $token = false;
        if ($openIdentifier = $request->get("openid_identifier", false)) {
            $token = new OpenIdToken($openIdentifier);
            $token->setBeginning(true);
        } else if ($openIdentifier = $request->get("openid_op_endpoint", false)) {
            $token = new OpenIdToken($openIdentifier);
            $token->setBeginning(false);
        }

        if (false == $token) {
            return null;
        }

        $result = $this->authenticationManager->authenticate($token);
		if($result instanceof OpenIdToken && $url = $result->getAuthenticateUrl()) {
	        return new RedirectResponse($url);
        }

		return $result;
    }	
}
