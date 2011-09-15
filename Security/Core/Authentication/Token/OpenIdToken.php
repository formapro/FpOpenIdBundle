<?php
namespace Fp\OpenIdBundle\Security\Core\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class OpenIdToken extends AbstractToken
{
    protected $openIdentifier;

    protected $authenticateUrl;

    protected $beginning;

    public function __construct($openIdentifier, array $roles = array())
    {
        parent::__construct($roles);
        parent::setAuthenticated(count($roles) > 0);

        $this->openIdentifier = $openIdentifier;
        $this->beginning = true;
    }

    public function isBeginning()
    {
        return $this->beginning;
    }

    public function setBeginning($boolean)
    {
        $this->beginning = $boolean;
    }

    public function getOpenIdentifier()
    {
        return $this->openIdentifier;
    }

    public function getAuthenticateUrl()
    {
        return $this->authenticateUrl;
    }

    public function setAuthenticateUrl($url)
    {
        return $this->authenticateUrl = $url;
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
}