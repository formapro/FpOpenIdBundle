<?php

namespace Fp\OpenIdBundle\RelyingParty;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

use Fp\OpenIdBundle\Security\Core\Authentication\Token\OpenIdToken;

class RecoveredFailureRelyingParty implements RelyingPartyInterface
{
    const RECOVERED_QUERY_PARAMETER = 'openid_failure_recovered';

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request)
    {
        if (false == $request->get(self::RECOVERED_QUERY_PARAMETER)) {
            return false;
        }
        if (false == $error = $request->getSession()->get(Security::AUTHENTICATION_ERROR)) {
            return false;
        }
        if (false == $error->getToken() instanceof OpenIdToken) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function manage(Request $request)
    {
        if (false == $this->supports($request)) {
            throw new \InvalidArgumentException('The relying party does not support the request');
        }

        $error = $request->getSession()->get(Security::AUTHENTICATION_ERROR);

        $request->getSession()->remove(Security::AUTHENTICATION_ERROR);

        return new IdentityProviderResponse(
            $error->getToken()->getIdentity(),
            $error->getToken()->getAttributes()
        );
    }
}
