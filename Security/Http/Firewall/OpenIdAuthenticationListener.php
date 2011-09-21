<?php
namespace Fp\OpenIdBundle\Security\Http\Firewall;

use Symfony\Component\Security\Http\Firewall\AbstractAuthenticationListener;
use Symfony\Component\HttpFoundation\Request;

use Fp\OpenIdBundle\Security\Core\Authentication\Token\OpenIdToken;

class OpenIdAuthenticationListener extends AbstractAuthenticationListener
{
    protected function attemptAuthentication(Request $request)
    {
        $token = $this->attemptDefineToken($request);
        if (false == $token) {
            return null;
        }

        $token->setResponse($this->getExternalResponse($request));

        $result = $this->authenticationManager->authenticate($token);

        if($result instanceof OpenIdToken && $url = $result->getAuthenticateUrl()) {
            return $this->httpUtils->createRedirectResponse($request, $url);
        }
        if($result instanceof OpenIdToken && $url = $result->getApproveUrl()) {
            return $this->httpUtils->createRedirectResponse($request, $url);
        }

        return $result;
    }

    protected function getExternalResponse(Request $request)
    {
        $response = $request->query->all();
        array_walk($response, function(&$value, $key) {
            if (false === strpos($key, 'openid')) {
                $value = null;
            }
        });

        return array_filter($response);
    }
    

    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * 
     * @return \Fp\OpenIdBundle\Security\Core\Authentication\Token\OpenIdToken|null
     */
    protected function attemptDefineToken(Request $request)
    {
        $token = null;
        if ($identifier = $request->get("openid_identifier", false)) {

            $token = new OpenIdToken($identifier);
            $token->setState('verify');

        } else if ($identifier = $request->get("openid_op_endpoint", false)) {

            $token = new OpenIdToken($identifier);
            $token->setState('complete');

        } elseif ($identifier = $request->get("openid_approved", false)) {

            $token = new OpenIdToken($identifier);
            $token->setState('approved');
            
        }

        return $token;
    }
}
