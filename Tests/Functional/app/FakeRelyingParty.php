<?php
namespace Fp\OpenIdBundle\Tests\Functional;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Fp\OpenIdBundle\RelyingParty\RelyingPartyInterface;
use Fp\OpenIdBundle\RelyingParty\IdentityProviderResponse;

/**
 * @author Kotlyar Maksim <kotlyar.maksim@gmail.com>
 * @since 4/27/12
 */
class FakeRelyingParty implements RelyingPartyInterface
{
    const VERIFY_REQUEST = 'openid_identifier';

    const VERIFY_IDENTIFIER = 'fakerelyingparty';
    
    const COMPLETE_REQUEST = 'fakerelyingpartyComplete';
    
    const REDIRECT_URL = 'http://fakeopenidprovider.com/verify';
    
    const IDENTITY_PROVIDED = 'fakerelyingpartyIdentity';
    
    /**
     * {@inheritdoc}
     */
    public function supports(Request $request)
    {
        return 
            ($request->get(self::VERIFY_REQUEST) && $request->get(self::VERIFY_REQUEST) == self::VERIFY_IDENTIFIER) || 
            $request->get(self::COMPLETE_REQUEST)
        ; 
    }

    /**
     * {@inheritdoc}
     */
    public function manage(Request $request)
    {
        if ($request->get(self::VERIFY_REQUEST)) {
            return new RedirectResponse(self::REDIRECT_URL);
        }
        if ($request->get(self::COMPLETE_REQUEST)) {
            return new IdentityProviderResponse(self::IDENTITY_PROVIDED);        
        }
    }
}
