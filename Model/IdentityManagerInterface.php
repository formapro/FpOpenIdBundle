<?php
namespace Fp\OpenIdBundle\Model;

interface IdentityManagerInterface
{
    /**
     * @param string $identity
     *
     * @return \Fp\OpenIdBundle\Model\IdentityInterface|null
     */
    function findByIdentity($identity);

    /**
     * @return \Fp\OpenIdBundle\Model\IdentityInterface
     */
    function create();

    /**
     * @param \Fp\OpenIdBundle\Model\IdentityInterface $openIdIdentity
     *
     * @return void
     */
    function update(IdentityInterface $identity);

    /**
     * @param \Fp\OpenIdBundle\Model\IdentityInterface $openIdIdentity
     *
     * @return void
     */
    function delete(IdentityInterface $identity);
}
