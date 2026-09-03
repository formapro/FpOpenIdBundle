<?php

namespace Fp\OpenIdBundle\RelyingParty;

class IdentityProviderResponse implements \Serializable
{
    /**
     * @var string
     */
    protected $identity;

    /**
     * @var array
     */
    protected $attributes;

    /**
     * @param string $identity
     * @param array $attributes
     */
    public function __construct($identity, array $attributes = array())
    {
        $this->identity = $identity;
        $this->attributes = $attributes;
    }

    /**
     * @return string
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize(array($this->identity, $this->attributes));
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        list($this->identity, $this->attributes) = unserialize($serialized);
    }
}
