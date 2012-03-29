<?php
namespace Fp\OpenIdBundle\Model;

class Identity implements IdentityInterface
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $identity;

    /**
     * @var array
     */
    protected $attributes;

    /**
     * @return void
     */
    public function __construct()
    {
        $this->attributes = array();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * {@inheritdoc}
     */
    public function setIdentity($identity)
    {
        if ($this->identity) {
            throw new \LogicException('The identity was set before. It is not allowed update it');
        }

        $this->identity = $identity;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (string) $this->getIdentity();
    }
}