<?php
namespace Fp\OpenIdBundle\Security\Core\Authentication\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use Fp\OpenIdBundle\Security\Core\Authentication\Token\OpenIdToken;
use Fp\OpenIdBundle\Model\IdentityManagerInterface;
use Fp\OpenIdBundle\Model\UserIdentityInterface;

class OpenIdAuthenticationProvider implements AuthenticationProviderInterface
{
    /**
     * @var array
     */
    protected $roles;

    /**
     * @var     \Fp\OpenIdBundle\Model\IdentityManagerInterface|null
     */
    protected $identityManager;

    /**
     * @param array $roles
     */
    public function __construct(array $roles = array(), IdentityManagerInterface $identityManager = null)
    {
        $this->roles = $roles;
        $this->identityManager = $identityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(TokenInterface $token)
    {
        if (false == $this->supports($token)) {
            return null;
        }

        $roles = $this->roles;
        $user = $this->guessUser($token);
        $identity = $token->getIdentity();
        if ($this->identityManager) {
            if (false == $identityModel = $this->identityManager->findByIdentity($identity)) {
                $identityModel = $this->identityManager->create();
                $identityModel->setIdentity($identity);
                $identityModel->setAttributes($token->getAttributes());

                $this->identityManager->update($identityModel);
            }

            if ($identityModel instanceof UserIdentityInterface && $identityModel->getUser()) {
                $roles = $identityModel->getUser()->getRoles();
                $user = $identityModel->getUser();
            }

            $identity = $identityModel;
        }

        $newToken = new OpenIdToken($identity, $roles);
        $newToken->setAuthenticated(true);
        $newToken->setAttributes($token->getAttributes());
        $newToken->setUser($user);

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