<?php
namespace Fp\OpenIdBundle\Consumer;

interface ConsumerInterface
{
    /**
     * @abstract
     *
     * @param string $identifier
     * @param string $returnUrl
     *
     * @throws Exception if it is not possible to create auth url.
     *
     * @return string
     */
    public function authenticateUrl($identifier, $returnUrl, array $parameters = array());

    /**
     * @abstract
     *
     * @param array $response
     * @param string $returnUrl
     *
     * @return array 
     */
    public function complete(array $response, $returnUrl);
}