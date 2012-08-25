<?php
namespace Fp\OpenIdBundle\Document;

use Doctrine\ODM\MongoDB\DocumentManager;

use Fp\OpenIdBundle\Model\IdentityManagerInterface;
use Fp\OpenIdBundle\Model\IdentityInterface;

class IdentityManager implements IdentityManagerInterface
{
    protected $documentManager;

    protected $identityClass;

    public function __construct(DocumentManager $documentManager, $identityClass)
    {
        $this->documentManager = $documentManager;
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
        $this->documentManager->persist($identity);
        $this->documentManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function delete(IdentityInterface $identity)
    {
        $this->documentManager->remove($identity);
        $this->documentManager->flush();
    }

    /**
     * @return \Doctrine\ODM\MongoDB\DocumentRepository
     */
    protected function getIdentityRepository()
    {
        return $this->documentManager->getRepository($this->identityClass);
    }
}