<?php
namespace Fp\OpenIdBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Fp\OpenIdBundle\Event\AuthenticationEvent;
use Fp\OpenIdBundle\Consumer\ConsumerInterface;

class TrustRootListener implements EventSubscriberInterface
{
    /**
     * @var array
     */
    protected $consumers = array();

    /**
     * @param \Fp\OpenIdBundle\Consumer\ConsumerInterface $consumer
     *
     * @return void
     */
    public function addConsumer(ConsumerInterface $consumer)
    {
       $this->consumers[] = $consumer;
    }

    /**
     * @param \Fp\OpenIdBundle\Event\AuthenticationEvent $event
     *
     * @return void
     */
    public function onOpenidBeforeAuthentication(AuthenticationEvent $event)
    {
        $request = $event->getRequest();
        foreach ($this->consumers as $consumer) {
            $consumer->changeTrustRoot($request->getHttpHost());
        }
    }

    /**
     * {@inheritdoc}
     */
    static function getSubscribedEvents()
    {
        return array('fp_openid.before_authentication' => 'onOpenidBeforeAuthentication');
    }
}