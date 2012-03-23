<?php
namespace Fp\OpenIdBundle\Security\Core\Authentication\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use Fp\OpenIdBundle\Security\Core\Authentication\Token\OpenIdToken;

class OpenIdAuthenticationProvider implements AuthenticationProviderInterface
{
    /**
     * @var array
     */
    protected $roles;

    /**
     * @param array $roles
     */
    public function __construct(array $roles = array())
    {
        $this->roles = $roles;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(TokenInterface $token)
    {
        if (false == $this->supports($token)) {
            return null;
        }

        $newToken = new OpenIdToken($token->getIdentity(), $this->roles);
        $newToken->setAttributes($token->getAttributes());
        $newToken->setUser('openid');

        return $newToken;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(TokenInterface $token)
    {
        return $token instanceof OpenIdToken;
    }
}