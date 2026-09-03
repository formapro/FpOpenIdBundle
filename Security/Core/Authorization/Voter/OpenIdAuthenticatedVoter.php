<?php
namespace Fp\OpenIdBundle\Security\Core\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use Fp\OpenIdBundle\Security\Core\Authentication\Token\OpenIdToken;

class OpenIdAuthenticatedVoter implements VoterInterface
{
    const IS_AUTHENTICATED_OPENID = 'IS_AUTHENTICATED_OPENID';

    /**
     * {@inheritdoc}
     */
    public function supportsAttribute($attribute)
    {
        return null !== $attribute && self::IS_AUTHENTICATED_OPENID === $attribute;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        foreach ($attributes as $attribute) {
            if (false == $this->supportsAttribute($attribute)) {
                continue;
            }

            return $token instanceof OpenIdToken ?
                VoterInterface::ACCESS_GRANTED :
                VoterInterface::ACCESS_DENIED
            ;
        }

        return VoterInterface::ACCESS_ABSTAIN;
    }
}
