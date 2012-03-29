<?php
namespace Fp\OpenIdBundle\Security\Http\Firewall;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Firewall\AbstractAuthenticationListener;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ParameterBag;

use Fp\OpenIdBundle\Client\ClientInterface;

abstract class AbstractOpenIdAuthenticationListener extends AbstractAuthenticationListener
{
    /**
     * @var \Fp\OpenIdBundle\Client\ClientInterface
     */
    private $client;

    /**
     * @var null|\Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager, SessionAuthenticationStrategyInterface $sessionStrategy, HttpUtils $httpUtils, $providerKey, array $options = array(), AuthenticationSuccessHandlerInterface $successHandler = null, AuthenticationFailureHandlerInterface $failureHandler = null, LoggerInterface $logger = null, EventDispatcherInterface $dispatcher = null)
    {
        $options = array_merge(array(
            'required_parameters' => array(),
            'optional_parameters' => array(),
        ), $options);

        parent::__construct($securityContext, $authenticationManager, $sessionStrategy, $httpUtils,$providerKey, $options, $successHandler, $failureHandler, $logger, $dispatcher);

        $this->dispatcher = $dispatcher;
    }

    /**
     * The client is required for the listener but since It is not possible overwrite constructor I use setter with the check in getter
     *
     * @param \Fp\OpenIdBundle\Client\ClientInterface $client
     */
    public function setClient(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @throws \RuntimeException
     *
     * @return \Fp\OpenIdBundle\Client\ClientInterface
     */
    protected function getClient()
    {
        if (false == $this->client) {
            throw new \RuntimeException('The client is required for the listener work, but it was not set. Seems like miss configuration');
        }

        return $this->client;
    }

    /**
     * @return null|\Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    protected function requiresAuthentication(Request $request)
    {
        if (false == parent::requiresAuthentication($request)) {
            return false;
        }

        return $this->getClient()->canManage($request);
    }
}