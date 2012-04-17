<?php
namespace Fp\OpenIdBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface;

interface UserIdentityInterface extends IdentityInterface
{
    /**
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     *
     * @return void
     */
    function setUser(UserInterface $user);

    /**
     * @return \Symfony\Component\Security\Core\User\UserInterface|null
     */
    function getUser();
}
