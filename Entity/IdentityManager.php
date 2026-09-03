<?php
namespace Fp\OpenIdBundle\Entity;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectRepository;
use Fp\OpenIdBundle\Model\IdentityInterface;
use Fp\OpenIdBundle\Model\IdentityManagerInterface;

class IdentityManager implements IdentityManagerInterface
{
    protected $entityManager;

    protected $identityClass;

    public function __construct(EntityManagerInterface $entityManager, $identityClass)
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
     * @return ObjectRepository
     */
    protected function getIdentityRepository()
    {
        return $this->entityManager->getRepository($this->identityClass);
    }
}
