<?php
namespace Fp\OpenIdBundle\RelyingParty\Exception;

use Symfony\Component\Security\Core\Exception\AuthenticationServiceException;

class OpenIdAuthenticationValidationFailedException extends AuthenticationServiceException
{    
}