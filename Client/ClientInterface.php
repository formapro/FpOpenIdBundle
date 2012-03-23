<?php
namespace Fp\OpenIdBundle\Client;

use Symfony\Component\HttpFoundation\Request;

interface ClientInterface
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return bool
     */
    function canManage(Request $request);

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws RuntimeException if cannot manage the Request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Fp\OpenIdBundle\Security\Core\Authentication\Token\OpenIdToken
     */
    function manage(Request $request);
}