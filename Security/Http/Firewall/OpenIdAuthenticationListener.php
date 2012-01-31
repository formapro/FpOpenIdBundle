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

use Fp\OpenIdBundle\Security\Core\Authentication\Token\OpenIdToken;
use Fp\OpenIdBundle\Event\AuthenticationEvent;

class OpenIdAuthenticationListener extends AbstractAuthenticationListener
{
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcher
     */
    protected $dispatcher;

    /**
     * {@inheritdoc}
     */
    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager, SessionAuthenticationStrategyInterface $sessionStrategy, HttpUtils $httpUtils, $providerKey, array $options = array(), AuthenticationSuccessHandlerInterface $successHandler = null, AuthenticationFailureHandlerInterface $failureHandler = null, LoggerInterface $logger = null, EventDispatcherInterface $dispatcher = null)
    {
        $this->dispatcher = $dispatcher;

        parent::__construct($securityContext, $authenticationManager, $sessionStrategy, $httpUtils, $providerKey, $options, $successHandler, $failureHandler, $logger, $dispatcher);
    }

    /**
     * {@inheritdoc}
     */
    protected function attemptAuthentication(Request $request)
    {
        $token = $this->attemptDefineToken($request);
        if (false == $token) {
            return null;
        }

        $this->dispatcher->dispatch('fp_openid.before_authentication', new AuthenticationEvent($request, $token));

        $token->setResponse($request->query->all());

        $result = $this->authenticationManager->authenticate($token);

        if($result instanceof OpenIdToken && $url = $result->getAuthenticateUrl()) {
            return $this->httpUtils->createRedirectResponse($request, $url);
        }
        if($result instanceof OpenIdToken && $url = $result->getApproveUrl()) {
            return $this->httpUtils->createRedirectResponse($request, $url);
        }
        if($result instanceof OpenIdToken && $url = $result->getCancelUrl()) {
            return $this->httpUtils->createRedirectResponse($request, $url);
        }

        return $result;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Fp\OpenIdBundle\Security\Core\Authentication\Token\OpenIdToken|null
     */
    protected function attemptDefineToken(Request $request)
    {
        $token = null;
        if ($request->get('openid_mode', false) && 'cancel' == $request->get('openid_mode')) {
            $token = new OpenIdToken('canceled');
            $token->setState('cancel');
        } else if ($request->get('openid_mode', false) && 'verify' == $request->get('openid_mode')) {
            $token = new OpenIdToken();
            $token->setState('verify');
        } else if ($identifier = $request->get('openid_identifier', false)) {
            $token = new OpenIdToken($identifier);
            $token->setState('verify');
        } else if ($identifier = $request->get('openid_op_endpoint', false)) {
            $token = new OpenIdToken($identifier);
            $token->setState('complete');
        } else if ($identifier = $request->get('openid_approved', false)) {
            $token = new OpenIdToken($identifier);
            $token->setState('approved');
        }

        return $token;
    }
}
