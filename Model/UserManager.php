<?php
namespace Fp\OpenIdBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\AuthenticationServiceException;

use Fp\OpenIdBundle\Security\Core\User\UserManagerInterface;

class UserManager implements UserManagerInterface
{
    /**
     * @var IdentityManagerInterface
     */
    protected $identityManager;

    /**
     * @param IdentityManagerInterface $identityManager
     */
    public function __construct(IdentityManagerInterface $identityManager)
    {
        $this->identityManager = $identityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        return $this->loadUserByIdentity($username);
    }

    /**
     * @param string $identity
     *
     * @return UserInterface
     * @throws UsernameNotFoundException if identity does not implement UserIdentityInterface.
     * @throws UsernameNotFoundException if user identity does not a user instance set.
     *
     * @throws UsernameNotFoundException if identity not found.
     */
    protected function loadUserByIdentity($identity)
    {
        $identityModel = $this->identityManager->findByIdentity($identity);
        if (false == $identityModel instanceof IdentityInterface) {
            throw new UsernameNotFoundException(sprintf('Identity %s not found.', $identity));
        }
        if (false == $identityModel instanceof UserIdentityInterface) {
            throw new UsernameNotFoundException('Identity must implement UserIdentityInterface.');
        }
        if (false == $identityModel->getUser() instanceof UserInterface) {
            throw new UsernameNotFoundException('UserIdentity must have a user to be set previously.');
        }

        return $identityModel->getUser();
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        throw new UnsupportedUserException('The provider cannot refresh any kind of user.');
    }

    /**
     * Should not support any user classes.
     *
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function createUserFromIdentity($identity, array $attributes = array())
    {
        throw new AuthenticationServiceException('The manager does not implement createUserFromIdentity method. Please extend manager and overwrite the method with your logic.');
    }
}
