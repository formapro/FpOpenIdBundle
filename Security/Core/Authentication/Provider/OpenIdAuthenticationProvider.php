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
        $newToken->setAuthenticated(true);
        $newToken->setAttributes($token->getAttributes());
        $newToken->setUser($this->guessUser($token));

        return $newToken;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(TokenInterface $token)
    {
        return $token instanceof OpenIdToken;
    }

    /**
     * @param \Fp\OpenIdBundle\Security\Core\Authentication\Token\OpenIdToken $token
     *
     * @return string
     */
    protected function guessUser(OpenIdToken $token)
    {
        $attributes = array_merge(array(
            'contact/email' => null,
            'namePerson/first' => null,
            'namePerson/last' => null,
        ), $token->getAttributes());

        $username = 'a user';
        if ($attributes['contact/email']) {
            $username = $attributes['contact/email'];
        } else if ($attributes['namePerson/first']) {
            $username = $attributes['namePerson/first'];

            if ($attributes['namePerson/last']) {
                $username .= " {$attributes['namePerson/last']}";
            }
        }

        $provider = parse_url($token->getIdentity(), PHP_URL_HOST);

        return "{$username} by {$provider}";
    }
}