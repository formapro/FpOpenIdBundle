<?php
namespace Fp\OpenIdBundle\RelyingParty;

use Symfony\Component\HttpFoundation\Request;

interface RelyingPartyInterface
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return bool
     */
    function supports(Request $request);

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \RuntimeException if cannot manage the Request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Fp\OpenIdBundle\RelyingParty\IdentityProviderResponse
     */
    function manage(Request $request);
}