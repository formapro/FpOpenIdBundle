<?php
namespace Acme\DemoBundle\EventListener;

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
     * @var \Fp\OpenIdBundle\Model\IdentityManagerInterface|null
     */
    private $identityManager;

    /**
     * @param \Fp\OpenIdBundle\Model\IdentityManagerInterface|null $identityManager
     */
    public function __construct(IdentityManagerInterface $identityManager = null)
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
        if (false == $this->getIdentityManager()) {
            return;
        }
        if ($event->getResponse()) {
            return;
        }

        $modelIdentity = $this->findOrCreateIdentity($event->getIdentity());
        if (false == $modelIdentity instanceof UserIdentityInterface) {
            return;
        }
        if ($modelIdentity->getUser()) {
            return;
        }

        $result = $this->assignUser($modelIdentity, $event->getRequest());
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
     * @param \Fp\OpenIdBundle\Model\UserIdentityInterface $identity
     *
     * @return \Symfony\Component\HttpFoundation\Response|Symfony\Component\Security\Core\User\UserInterface
     */
    abstract protected function assignUser(UserIdentityInterface $identity, Request $request);

    /**
     * @return \Fp\OpenIdBundle\Model\IdentityManagerInterface|null
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
    private function findOrCreateIdentity($identity)
    {
        if ($modelIdentity = $this->identityManager->findByIdentity($identity)) {
            return $modelIdentity;
        }

        $modelIdentity = $this->identityManager->create($identity);
        $modelIdentity->setIdentity($identity);

        return $modelIdentity;
    }

    static function getSubscribedEvents()
    {
        return array(
            SecurityEvents::IDENTITY_PROVIDED => 'onIdentityProvided',
        );
    }
}