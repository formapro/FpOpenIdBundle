<?php
namespace Fp\OpenIdBundle\RelyingParty;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

use Fp\OpenIdBundle\RelyingParty\IdentityProviderResponse;

class RelyingPartyCollection implements RelyingPartyInterface
{
    /**
     * @var array
     */
    protected $relyingParties = array();

    /**
     * @param RelyingPartyInterface $relyingParty
     */
    public function append(RelyingPartyInterface $relyingParty)
    {
        array_push($this->relyingParties, $relyingParty);
    }

    /**
     * @param RelyingPartyInterface $relyingParty
     */
    public function prepend(RelyingPartyInterface $relyingParty)
    {
        array_unshift($this->relyingParties, $relyingParty);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request)
    {
        var_dump($this->findRelyingPartySupportedRequest($request));

        return (bool) $this->findRelyingPartySupportedRequest($request);
    }

    /**
     * {@inheritdoc}
     */
    public function manage(Request $request)
    {
        if (false == $relyingParty = $this->findRelyingPartySupportedRequest($request)) {
            throw new \InvalidArgumentException('The relying party does not support the request');
        }

        return $relyingParty->manage($request);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Fp\OpenIdBundle\RelyingPartyRelyingPartyInterface|null
     */
    protected function findRelyingPartySupportedRequest(Request $request)
    {
        foreach ($this->relyingParties as $relyingParty) {
            if ($relyingParty->supports($request)) {
                return $relyingParty;
            }
        }
    }
}