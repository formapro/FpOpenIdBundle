<?php
namespace Fp\OpenIdBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface;

class UserIdentity extends Identity implements UserIdentityInterface
{
    /**
     * @var \Symfony\Component\Security\Core\User\UserInterface
     */
    protected $user;
    
    /**
     * {@inheritdoc}
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getUser()
    {
        return $this->user;
    }
}