<?php
namespace Fp\OpenIdBundle\Security\Core\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

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
     * @return string
     */
    public function getCredentials()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function __serialize(): array
    {
        return array(
            $this->providerKey,
            $this->identity,
            \is_callable('parent::__serialize') ? parent::__serialize() : unserialize(parent::serialize()),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function __unserialize(array $data): void
    {
        list($this->providerKey, $this->identity, $parentData) = $data;

        $parentData = \is_array($parentData) ? $parentData : unserialize($parentData);

        if (\is_callable('parent::__serialize')) {
            parent::__unserialize($parentData);
        } else {
            parent::unserialize(serialize($parentData));
        }
    }
}
