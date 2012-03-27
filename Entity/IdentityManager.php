<?php
namespace Fp\OpenIdBundle\Entity;

use Doctrine\ORM\EntityManager;

use Fp\OpenIdBundle\Model\IdentityManagerInterface;
use Fp\OpenIdBundle\Model\IdentityInterface;

class IdentityManager implements  IdentityManagerInterface
{
    protected $entityManager;

    protected $identityClass;

    public function __construct(EntityManager $entityManager, $identityClass)
    {
        $this->entityManager = $entityManager;
        $this->identityClass = $identityClass;
    }

    /**
     * {@inheritdoc}
     */
    public function findByIdentity($identity)
    {
        return $this->getIdentityRepository()->findOneBy(array(
            'identity' => $identity
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return new $this->identityClass;
    }

    /**
     * {@inheritdoc}
     */
    public function update(IdentityInterface $identity)
    {
        $this->entityManager->persist($identity);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function delete(IdentityInterface $identity)
    {
        $this->entityManager->remove($identity);
        $this->entityManager->flush();
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getIdentityRepository()
    {
        return $this->entityManager->getRepository($this->identityClass);
    }
}