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
     * @var null|\Symfony\Component\Security\Core\User\UserProviderInterface
     */
    protected $userProvider;

    /**
     * @var null|\Symfony\Component\Security\Core\User\UserCheckerInterface
     */
    protected $userChecker;

    /**
     * @var bool
     */
    protected $createIfNotExists;

    /**
     * @param null|\Symfony\Component\Security\Core\User\UserProviderInterface $userProvider
     * @param null|\Symfony\Component\Security\Core\User\UserCheckerInterface $userChecker
     * @param bool $createIfNotExists
     */
    public function __construct(UserProviderInterface $userProvider = null, UserCheckerInterface $userChecker = null, $createIfNotExists = false)
    {
        if (null !== $userProvider && null === $userChecker) {
            throw new \InvalidArgumentException('$userChecker cannot be null, if $userProvider is not null.');
        }

        if ($createIfNotExists && !$userProvider instanceof UserManagerInterface) {
            throw new \InvalidArgumentException('The $userProvider must implement UserManagerInterface if $createIfNotExists is true.');
        }

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
            throw new AuthenticationServiceException($e->getMessage(), null, (int) $e->getCode(), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports(TokenInterface $token)
    {
        return $token instanceof OpenIdToken;
    }

    /**
     * @param string $identity
     *
     * @throws \RuntimeException if provider did not provide a user implementation.
     *
     * @return \Symfony\Component\Security\Core\User\UserInterface
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
     * @param \Fp\OpenIdBundle\Security\Core\Authentication\Token\OpenIdToken $token
     *
     * @return string
     */
    protected function getDefaultUser($identity, array $attributes)
    {
        return $identity;

//        $attributes = array_merge(array(
//            'contact/email' => null,
//            'namePerson/first' => null,
//            'namePerson/last' => null,
//        ), $attributes);
//
//        $username = 'a user';
//        if ($attributes['contact/email']) {
//            $username = $attributes['contact/email'];
//        } else if ($attributes['namePerson/first']) {
//            $username = $attributes['namePerson/first'];
//
//            if ($attributes['namePerson/last']) {
//                $username .= " {$attributes['namePerson/last']}";
//            }
//        }
//
//        $provider = parse_url($identity, PHP_URL_HOST);
//
//        return "{$username} by {$provider}";
    }

    /**
     * @param string $identity
     * @param array $attributes
     * @param array $roles
     * @param mixed $user
     *
     * @return \Fp\OpenIdBundle\Security\Core\Authentication\Token\OpenIdToken
     */
    protected function createAuthenticatedToken($identity, array $attributes, array $roles, $user)
    {
        if ($user instanceof UserInterface) {
            $this->userChecker->checkPostAuth($user);
        }

        $newToken = new OpenIdToken($identity, $roles);
        $newToken->setUser($user);
        $newToken->setAttributes($attributes);
        $newToken->setAuthenticated(true);

        return $newToken;
    }
}