<?php
namespace Fp\OpenIdBundle\Security\Core\Authentication\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Routing\RouterInterface;

use Fp\OpenIdBundle\Security\Core\Authentication\Token\TokenPersister;
use Fp\OpenIdBundle\Security\Core\Authentication\Token\OpenIdToken;
use Fp\OpenIdBundle\Consumer\ConsumerInterface;


class OpenIdAuthenticationProvider implements AuthenticationProviderInterface
{
    protected $consumer;

    protected $router;

    protected $tokenPersister;

    protected $parameters;

    public function __construct(ConsumerInterface $consumer, RouterInterface $router, TokenPersister $tokenPersister, array $parameters)
    {
        $this->consumer = $consumer;
        $this->router = $router;
        $this->tokenPersister = $tokenPersister;

        $this->parameters = array_merge(array(
            'return_route' => null,
            'approve_route' => null,
            'roles' => array()), $parameters);
    }

    public function authenticate(TokenInterface $token)
    {
        if (false == $this->supports($token)) {
            return null;
        }

        $processState = 'process' . ucfirst($token->getState());

        return $this->$processState($token);
    }

    public function supports(TokenInterface $token)
    {
        if (false == ($token instanceof OpenIdToken)) {
            return false;
        }

        return in_array($token->getState(), array('verify', 'complete', 'approved'));
    }

    public function processVerify(OpenIdToken $token)
    {
        $token->setAuthenticateUrl(
            $this->consumer->authenticateUrl($token->getIdentifier(), $this->getReturnUrl()));

        return $token;
    }

    public function processComplete(OpenIdToken $token)
    {
        $attributes = $this->consumer->complete($token->getResponse(), $this->getReturnUrl());

        $token = new OpenIdToken($attributes['identity'], $this->parameters['roles']);
        $token->setAttributes($attributes);

        if ($this->parameters['approve_route']) {
            $this->tokenPersister->set($token);
            $token->setApproveUrl($this->router->generate($this->parameters['approve_route'], array(), true));
        }

        return $token;
    }

    public function processApproved(OpenIdToken $token)
    {
        $token = $this->tokenPersister->get();
        if (false == $token) {
            throw new \RuntimeException('The token persister does not contains a token');
        }
        if (false == $token->getUser()) {
            throw new AuthenticationException('Authentication approving was canceled');
        }

        return $token;
    }

    protected function getReturnUrl()
    {
        $route = $this->parameters['return_route'];

        return $this->router->generate($route, array(), true);
    }
}