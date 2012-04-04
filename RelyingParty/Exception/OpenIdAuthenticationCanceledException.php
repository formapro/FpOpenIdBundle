<?php
namespace Fp\OpenIdBundle\RelyingParty\Exception;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

class OpenIdAuthenticationCanceledException extends AuthenticationException
{
}