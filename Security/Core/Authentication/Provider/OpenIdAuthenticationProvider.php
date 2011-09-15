<?php
namespace Fp\OpenIdBundle\Security\Core\Authentication\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\NonceExpiredException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Fp\OpenIdBundle\Security\Core\Authentication\Token\OpenIdToken;

class OpenIdAuthenticationProvider implements AuthenticationProviderInterface
{
    protected $container;
	protected $parameters;

    public function __construct(ContainerInterface $container, array $parameters)
    {
        $this->container = $container;
        $this->parameters = array_merge(array(
            'routerService' => null,
            'lightOpenIdService' => null,
            'return_route' => null,
            'roles' => array(),
            'openid_required_options' => array(),
            'openid_optional_options' => array()), $parameters);
    }

    public function authenticate(TokenInterface $token)
    {
        if (false == $this->supports($token)) {
            return null;
        }

        return $token->isBeginning() ? $this->start($token) : $this->finish($token);
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof OpenIdToken;
    }

    public function start(OpenIdToken $token)
    {
        $lightOpenId = $this->getLightOpenId();

        $lightOpenId->identity = $token->getOpenIdentifier();
		$lightOpenId->returnUrl = $this->getRouter()->generate($this->parameters['return_route'], array(), true);
		$lightOpenId->required = $this->parameters['openid_required_options'];
		$lightOpenId->optional = $this->parameters['openid_optional_options'];

        $token->setAuthenticateUrl($lightOpenId->authUrl());
        
        return $token;
    }

    public function finish(OpenIdToken $token)
    {
        $lightOpenId = $this->getLightOpenId();
        if (false == $lightOpenId->validate()) {
            if($lightOpenId->mode == 'cancel') {
              throw new AuthenticationException('Authentication was canceled');
            }

           throw new AuthenticationException('Authentication is not valid');
        }
        
        $token = new OpenIdToken($lightOpenId->identity, $this->parameters['roles']);
        $token->setAttributes($lightOpenId->getAttributes());

        return $token;
    }

    protected function getRouter()
    {
        return $this->container->get($this->parameters['routerService']);
    }

    /**
     * @return \LightOpenID
     */
    protected function getLightOpenId()
    {
        return $this->container->get($this->parameters['lightOpenIdService']);
    }
}