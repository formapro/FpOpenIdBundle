<?php
namespace Fp\OpenIdBundle\Model;

interface IdentityInterface
{
    /**
     * @return mixed
     */
    function getId();

    /**
     * @return string
     */
    function getIdentity();

    /**
     * @param string $identity
     *
     * @return void
     */
    function setIdentity($identity);

    /**
     * Must return identity
     *
     * @return string
     */
    function __toString();

    /**
     * @return array
     */
    function getAttributes();

    /**
     * @param array $attributes
     *
     * @return void
     */
    function setAttributes(array $attributes);
}
