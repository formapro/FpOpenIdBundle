<?php
namespace Fp\OpenIdBundle\Security\Http\Event;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\Event;

use Fp\OpenIdBundle\Security\Core\Authentication\Token\OpenIdToken;

class IdentityProvidedEvent extends Event
{
    /**
     * @var string
     */
    protected $identity;

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * @var \Symfony\Component\HttpFoundation\Response
     */
    protected $response;

    public function __construct($identity, Request $request)
    {
        $this->identity = $identity;
        $this->request = $request;
    }

    /**
     * @return string
     */
    public function getIdentity()
    {
        return $this->request;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}