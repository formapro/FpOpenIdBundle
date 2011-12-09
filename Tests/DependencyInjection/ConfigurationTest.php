<?php
namespace Fp\OpenIdBundle\Tests\DepenedencyInjection;

use Symfony\Component\Config\Definition\Processor;

use Fp\OpenIdBundle\DependencyInjection\Configuration;

class ConfigurationTest extends  \PHPUnit_Framework_TestCase
{
    protected $fullConfigs = array();

    public function setUp()
    {
        $this->fullConfigs = array('fp_open_id' => array(
            'provider' => array(
                'return_route' => 'http://foo.bar/return',
                'cancel_route' => 'http://foo.bar/cancel',
                'approve_route' => 'http://foo.bar/approve',
                'roles' => array(
                    'user',
                    'publisher',
                    'manager'
                ),
            ),
            'consumers' => array(
                'light_open_id' => array(
                    'required' => array(
                        'contact/email',
                        'namePerson/first',
                        'namePerson/last',
                    ),
                    'optional' => array(
                        'foo' => 'bar',
                        'bar' => 'foo',
                    ),
                    'trust_root' => 'http://foo.bar',
                    'default' => true,
                ),
                'another_one' => true
            ),
        ));
    }

    /**
     * @test
     *
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage The child node "provider" at path "fp_open_id" must be configured.
     */
    public function shouldThrowOnEmptyConfig()
    {
        $this->processConfiguration(array());
    }

    /**
     * @test
     *
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage The child node "provider" at path "fp_open_id" must be configured.
     */
    public function shouldRequireProviderNode()
    {
        unset($this->fullConfigs['fp_open_id']['provider']);

        $this->processConfiguration($this->fullConfigs);
    }

    /**
     * @test
     *
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage The child node "consumers" at path "fp_open_id" must be configured.
     */
    public function shouldRequireConsumersNode()
    {
        unset($this->fullConfigs['fp_open_id']['consumers']);

        $this->processConfiguration($this->fullConfigs);
    }

    /**
     * @test
     *
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage The child node "return_route" at path "fp_open_id.provider" must be configured.
     */
    public function shouldRequireReturnRouteNode()
    {
        unset($this->fullConfigs['fp_open_id']['provider']['return_route']);

        $this->processConfiguration($this->fullConfigs);
    }

    /**
     * @test
     *
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage The path "fp_open_id.provider.return_route" cannot contain an empty value, but got null.
     */
    public function shouldNotBeEmptyReturnRouteNode()
    {
        $this->fullConfigs['fp_open_id']['provider']['return_route'] = null;

        $this->processConfiguration($this->fullConfigs);
    }


    /**
     * @test
     */
    public function shouldBeOptionalApproveRouteNode()
    {
        $this->fullConfigs['fp_open_id']['provider']['approve_route'] = null;

        $this->processConfiguration($this->fullConfigs);
    }

    /**
     * @test
     *
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage The child node "roles" at path "fp_open_id.provider" must be configured.
     */
    public function shouldRequireRolesNode()
    {
        unset($this->fullConfigs['fp_open_id']['provider']['roles']);

        $this->processConfiguration($this->fullConfigs);
    }

    protected function processConfiguration(array $configs)
    {
        $configuration = new Configuration();
        $processor = new Processor();

        return $processor->processConfiguration($configuration, $configs);
    }
}