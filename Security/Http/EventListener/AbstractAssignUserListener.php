<?php
namespace Fp\OpenIdBundle\Security\Http\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Fp\OpenIdBundle\Security\Http\Event\IdentityProvidedEvent;
use Fp\OpenIdBundle\Security\Http\SecurityEvents;
use Fp\OpenIdBundle\Model\IdentityManagerInterface;
use Fp\OpenIdBundle\Model\UserIdentityInterface;

abstract class AbstractAssignUserListener implements EventSubscriberInterface
{
    /**
     * @var \Fp\OpenIdBundle\Model\IdentityManagerInterface
     */
    private $identityManager;

    /**
     * @param \Fp\OpenIdBundle\Model\IdentityManagerInterface|null $identityManager
     */
    public function __construct(IdentityManagerInterface $identityManager)
    {
        $this->identityManager = $identityManager;
    }

    /**
     * @param \Fp\OpenIdBundle\Security\Http\Event\IdentityProvidedEvent $event
     *
     * @throws \RuntimeException
     *
     * @return void
     */
    public final function onIdentityProvided(IdentityProvidedEvent $event)
    {
        if ($event->getResponse()) {
            return;
        }

        $modelIdentity = $this->findOrCreateIdentity($event->getIdentity(), $event->getAttributes());
        if (false == $modelIdentity instanceof UserIdentityInterface) {
            return;
        }
        if ($modelIdentity->getUser()) {
            return;
        }

        $result = $this->assignUser($modelIdentity->getAttributes(), $event->getRequest());
        if ($result instanceof Response) {
            $event->setResponse($result);

            return;
        }

        if ($result instanceof UserInterface) {
            $modelIdentity->setUser($result);
            $this->getIdentityManager()->update($modelIdentity);

            return;
        }

        throw new \RuntimeException(sprintf(
            'The method %s::assignUser() must either return a Response or instance of UserInterface.',
            get_class($this)
        ));
    }

    /**
     * @param array $attributes
     *
     * @return \Symfony\Component\HttpFoundation\Response|Symfony\Component\Security\Core\User\UserInterface
     */
    abstract protected function assignUser(array $attributes, Request $request);

    /**
     * @return \Fp\OpenIdBundle\Model\IdentityManagerInterface
     */
    protected function getIdentityManager()
    {
        return $this->identityManager;
    }

    /**
     * @param string $identity
     *
     * @return \Fp\OpenIdBundle\Model\IdentityInterface
     */
    private function findOrCreateIdentity($identity, array $attributes = array())
    {
        if ($modelIdentity = $this->identityManager->findByIdentity($identity)) {
            return $modelIdentity;
        }

        $modelIdentity = $this->identityManager->create($identity);
        $modelIdentity->setIdentity($identity);
        $modelIdentity->setAttributes($attributes);

        return $modelIdentity;
    }

    static function getSubscribedEvents()
    {
        return array(
            SecurityEvents::IDENTITY_PROVIDED => 'onIdentityProvided',
        );
    }
}