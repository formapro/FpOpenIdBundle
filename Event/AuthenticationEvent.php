<?php
namespace Fp\OpenIdBundle\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\Event;

use Fp\OpenIdBundle\Security\Core\Authentication\Token\OpenIdToken;

class AuthenticationEvent extends Event
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var OpenIdToken
     */
    protected $token;

    public function __construct(Request $request, OpenIdToken $token)
    {
        $this->token = $token;
        $this->request = $request;
    }

    /**
     * @return \Fp\OpenIdBundle\Security\Core\Authentication\Token\OpenIdToken
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}