<?php
namespace Fp\OpenIdBundle\Security\Core\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Symfony\Component\HttpFoundation\Response;

class OpenIdToken extends AbstractToken
{
    /**
     * @var string
     */
    private $providerKey;

    /**
     * @var string
     */
    private $identity;

    /**
     * @param string
     * @param array $attributes
     * @param array $roles
     */
    public function __construct($providerKey, $identity, array $roles = array())
    {
        parent::__construct($roles);

        $this->setAuthenticated(count($this->getRoles()) > 0);

        $this->providerKey = $providerKey;
        $this->identity = $identity;
    }

    public function getProviderKey()
    {
        return $this->providerKey;
    }

    /**
     * @return string
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * @return void
     */
    public function getCredentials()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize(array($this->providerKey, $this->identity, parent::serialize()));
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($str)
    {
        list($this->providerKey, $this->identity, $parentStr) = unserialize($str);

        parent::unserialize($parentStr);
    }
}
