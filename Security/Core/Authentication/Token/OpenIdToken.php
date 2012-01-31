<?php
namespace Fp\OpenIdBundle\Security\Core\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class OpenIdToken extends AbstractToken
{
    protected $identifier;

    protected $authenticateUrl;

    protected $approveUrl;

    protected $cancelUrl;

    protected $state;

    protected $response = array();

    public function __construct($identifier = null, array $roles = array())
    {
        parent::__construct($roles);
        parent::setAuthenticated(count($roles) > 0);

        $this->identifier = $identifier;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function setResponse(array $response)
    {
        $this->response = $response;
    }

    public function setState($state)
    {
        $this->state = $state;
    }

    public function getState()
    {
        return $this->state;
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function getAuthenticateUrl()
    {
        return $this->authenticateUrl;
    }

    public function setAuthenticateUrl($url)
    {
        $this->authenticateUrl = $url;
    }

    public function getApproveUrl()
    {
        return $this->approveUrl;
    }

    public function setApproveUrl($url)
    {
        $this->approveUrl = $url;
    }

    public function getCancelUrl()
    {
        return $this->cancelUrl;
    }

    public function setCancelUrl($url)
    {
        $this->cancelUrl = $url;
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthenticated($isAuthenticated)
    {
        if ($isAuthenticated) {
            throw new \LogicException('Cannot set this token to trusted after instantiation.');
        }

        parent::setAuthenticated(false);
    }

    public function getCredentials()
    {
    }

    public function serialize()
    {
        return serialize(array($this->identifier, parent::serialize()));
    }

    public function unserialize($str)
    {
        list($this->identifier, $parentStr) = unserialize($str);
        parent::unserialize($parentStr);
    }
}