<?php
namespace Fp\OpenIdBundle\Security\User;

use Symfony\Component\Security\Core\User\UserProviderInterface;

interface UserManagerInterface extends UserProviderInterface
{
    /**
     * @param string $identity
     * @param array $attributes
     *
     * @return \Symfony\Component\Security\Core\User\UserInterface
     */
    function createUserFromIdentity($identity, array $attributes = array());
}