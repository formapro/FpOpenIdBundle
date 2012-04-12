<?php
namespace Fp\OpenIdBundle\RelyingParty;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

use Fp\OpenIdBundle\RelyingParty\IdentityProviderResponse;

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
        if (false == $error = $request->getSession()->get(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            return false;
        }
        if (false == $error->getExtraInformation() instanceof IdentityProviderResponse) {
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

        $error = $request->getSession()->get(SecurityContextInterface::AUTHENTICATION_ERROR);

        return $error->getExtraInformation();
    }
}