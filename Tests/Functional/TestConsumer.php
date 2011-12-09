<?php
namespace Fp\OpenIdBundle\Tests\Functional;

use Fp\OpenIdBundle\Consumer\ConsumerInterface;

class TestConsumer implements ConsumerInterface
{
    protected $trustRoot = 'test_trust_root';

    public function changeTrustRoot($trustRoot)
    {
        $this->trustRoot = $trustRoot;
    }

    public function authenticateUrl($identifier, $returnUrl)
    {
        return 'http://openid.provider.com?id=' . $identifier . '&trust_root=' . $this->trustRoot . '&return_url=' . $returnUrl;
    }

    public function complete(array $response, $returnUrl)
    {
        return array();
    }

    public function supports($identifier)
    {
        return true;
    }
}