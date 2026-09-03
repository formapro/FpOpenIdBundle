<?php

namespace Fp\OpenIdBundle\Security\Http\Firewall;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Firewall\AbstractAuthenticationListener;
use Symfony\Component\HttpFoundation\Request;

use Fp\OpenIdBundle\RelyingParty\RelyingPartyInterface;

abstract class AbstractOpenIdAuthenticationListener extends AbstractAuthenticationListener
{
    /**
     * @var RelyingPartyInterface $relyingParty
     */
    private $relyingParty;

    public function __construct(TokenStorageInterface $tokenStorage, AuthenticationManagerInterface $authenticationManager, SessionAuthenticationStrategyInterface $sessionStrategy, HttpUtils $httpUtils, $providerKey, AuthenticationSuccessHandlerInterface $successHandler, AuthenticationFailureHandlerInterface $failureHandler, array $options = array(), LoggerInterface $logger = null, EventDispatcherInterface $dispatcher = null)
    {
        $options = array_merge(array(
            'required_attributes' => array(),
            'optional_attributes' => array(),
            'target_path_parameter' => '_target_path',
        ), $options);
        
        parent::__construct($tokenStorage, $authenticationManager, $sessionStrategy, $httpUtils, $providerKey, $successHandler, $failureHandler, $options, $logger, $dispatcher);
    }

    /**
     * The relying party is required for the listener but since It is not possible overwrite constructor I use setter with the check in getter
     *
     * @param RelyingPartyInterface $relyingParty
     */
    public function setRelyingParty(RelyingPartyInterface $relyingParty)
    {
        $this->relyingParty = $relyingParty;
    }

    /**
     * @return RelyingPartyInterface
     * @throws \RuntimeException
     *
     */
    protected function getRelyingParty()
    {
        if (false == $this->relyingParty) {
            throw new \RuntimeException('The relying party is required for the listener work, but it was not set. Seems like miss configuration');
        }

        return $this->relyingParty;
    }

    /**
     * {@inheritdoc}
     */
    protected function requiresAuthentication(Request $request)
    {
        if (false == parent::requiresAuthentication($request)) {
            return false;
        }

        return $this->getRelyingParty()->supports($request);
    }
}
