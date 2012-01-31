<?php

namespace Fp\OpenIdBundle\Bridge\Consumer;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

use Fp\OpenIdBundle\Consumer\ConsumerInterface;
use LightOpenId;

class LightOpenIdConsumer implements ConsumerInterface
{
    protected $lightOpenIDClass;

    /**
     * @var LightOpenID
     */
    protected $lightOpenID;

    protected $parameters;

    public function __construct(array $parameters = array(), $lightOpenIDClass = 'LightOpenID')
    {
        $this->lightOpenIDClass = $lightOpenIDClass;

        $this->parameters = array_merge(array(
            'required' => array(),
            'optional' => array(),
            'trust_root' => '',
        ), $parameters);
    }

    /**
     * Check if a fixed identifier is set in config
     *
     * @return bool
     */
    public function hasConfiguredIdentifier()
    {
        return ($this->parameters['identifier'] !== null);
    }

    /**
     * {@inheritdoc}
     */
    public function changeTrustRoot($trustRoot)
    {
        $this->parameters['trust_root'] = $trustRoot;
        $this->lightOpenID = null;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticateUrl($identifier, $returnUrl)
    {
        $lightOpenId = $this->getLightOpenID();

        if ($this->hasConfiguredIdentifier()) {
            $lightOpenId->identity = $this->parameters['identifier'];
        } else if ($identifier !== null) {
            $lightOpenId->identity = $identifier;
        }

        $lightOpenId->returnUrl = $returnUrl;
        $lightOpenId->required = $this->parameters['required'];
        $lightOpenId->optional = $this->parameters['optional'];

        return $lightOpenId->authUrl();
    }

    /**
     * {@inheritdoc}
     */
    public function complete(array $response, $returnUrl)
    {
        $lightOpenId = $this->getLightOpenID();
        if (false == $lightOpenId->validate()) {
            if($lightOpenId->mode == 'cancel') {
              throw new AuthenticationException('Authentication was canceled');
            }

           throw new AuthenticationException('Authentication was not finished successfully');
        }

        return array('identity' => $lightOpenId->identity) + $lightOpenId->getAttributes();
    }

    /**
     * @return LightOpenID
     */
    protected function getLightOpenID()
    {
        if (false == $this->lightOpenID) {
            $this->lightOpenID = new $this->lightOpenIDClass($this->parameters['trust_root']);
        }

        return $this->lightOpenID;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($identifier)
    {
        // should support any kind of openid providers
        // can be used as default
        return true;
    }
}