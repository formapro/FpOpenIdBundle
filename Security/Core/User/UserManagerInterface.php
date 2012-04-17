<?php
namespace Fp\OpenIdBundle\Security\Core\User;

use Symfony\Component\Security\Core\User\UserProviderInterface;

interface UserManagerInterface extends UserProviderInterface
{
    /**
     * This method must throw UsernameNotFoundException if the user could not be created.
     *
     * @param string $identity
     * @param array $attributes
     *
     * @throws \Symfony\Component\Security\Core\Exception\UsernameNotFoundException if the user could not created
     *
     * @return \Symfony\Component\Security\Core\User\UserInterface
     */
    function createUserFromIdentity($identity, array $attributes = array());
}