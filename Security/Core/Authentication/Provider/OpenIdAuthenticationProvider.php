<?php
namespace Fp\OpenIdBundle\Security\Core\Authentication\Provider;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\AuthenticationServiceException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;

use Fp\OpenIdBundle\Security\Core\Authentication\Token\OpenIdToken;
use Fp\OpenIdBundle\Security\Core\User\UserManagerInterface;

class OpenIdAuthenticationProvider implements AuthenticationProviderInterface
{
    /**
     * @var string
     */
    protected $providerKey;

    /**
     * @var null|UserProviderInterface
     */
    protected $userProvider;

    /**
     * @var null|UserCheckerInterface
     */
    protected $userChecker;

    /**
     * @var bool
     */
    protected $createIfNotExists;

    /**
     * @param string $providerKey
     * @param null|UserProviderInterface $userProvider
     * @param null|UserCheckerInterface $userChecker
     * @param bool $createIfNotExists
     */
    public function __construct($providerKey, UserProviderInterface $userProvider = null, UserCheckerInterface $userChecker = null, $createIfNotExists = false)
    {
        if (null !== $userProvider && null === $userChecker) {
            throw new \InvalidArgumentException('$userChecker cannot be null, if $userProvider is not null.');
        }

        if ($createIfNotExists && !$userProvider instanceof UserManagerInterface) {
            throw new \InvalidArgumentException('The $userProvider must implement UserManagerInterface if $createIfNotExists is true.');
        }

        $this->providerKey = $providerKey;
        $this->userProvider = $userProvider;
        $this->userChecker = $userChecker;
        $this->createIfNotExists = $createIfNotExists;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(TokenInterface $token)
    {
        if (false == $this->supports($token)) {
            return null;
        }

        if ($token->getUser() instanceof UserInterface) {
            return $this->createAuthenticatedToken(
                $token->getIdentity(),
                $token->getAttributes(),
                $token->getUser()->getRoles(),
                $token->getUser()
            );
        }

        try {
            $user = $this->userProvider ?
                $this->getProviderUser($token->getIdentity(), $token->getAttributes()) :
                $this->getDefaultUser($token->getIdentity(), $token->getAttributes())
            ;

            return $this->createAuthenticatedToken(
                $token->getIdentity(),
                $token->getAttributes(),
                $user instanceof UserInterface ? $user->getRoles() : array(),
                $user
            );
        } catch (AuthenticationException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new AuthenticationServiceException($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports(TokenInterface $token)
    {
        return $token instanceof OpenIdToken && $this->providerKey === $token->getProviderKey();
    }

    /**
     * @param string $identity
     *
     * @throws \RuntimeException if provider did not provide a user implementation.
     *
     * @return UserInterface
     */
    protected function getProviderUser($identity, array $attributes)
    {
        try {
            $user = $this->userProvider->loadUserByUsername($identity);
        } catch (UsernameNotFoundException $e) {
            if (false == $this->createIfNotExists) {
                throw $e;
            }

            $user = $this->userProvider->createUserFromIdentity($identity, $attributes);
        }

        if (false == $user instanceof UserInterface) {
            throw new \RuntimeException('User provider did not return an implementation of user interface.');
        }

        return $user;
    }

    /**
     * @param OpenIdToken $token
     *
     * @return string
     */
    protected function getDefaultUser($identity, array $attributes)
    {
        return $identity;
    }

    /**
     * @param string $identity
     * @param array $attributes
     * @param array $roles
     * @param mixed $user
     *
     * @return OpenIdToken
     */
    protected function createAuthenticatedToken($identity, array $attributes, array $roles, $user)
    {
        if ($user instanceof UserInterface) {
            $this->userChecker->checkPostAuth($user);
        }

        $newToken = new OpenIdToken($this->providerKey, $identity, $roles);
        $newToken->setUser($user);
        $newToken->setAttributes($attributes);
        $newToken->setAuthenticated(true);

        return $newToken;
    }
}
