<?php
namespace Fp\OpenIdBundle\Tests\DepenedencyInjection;

use Symfony\Component\Config\Definition\Processor;

use Fp\OpenIdBundle\DependencyInjection\Configuration;

class ConfigurationTest extends  \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldAllowToUseWithoutAnyRequiredConfiguration()
    {
        $emptyConfig = array();

        $this->processConfiguration($emptyConfig);
    }

    /**
     * @test
     */
    public function shouldAllowToSetScalarDbDriver()
    {
        $config = array('fp_open_id' => array(
            'db_driver' => 'foo'
        ));

        $this->processConfiguration($config);
    }

    /**
     * @test
     *
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidTypeException
     * @expectedExceptionMessage Invalid type for path "fp_open_id.db_driver". Expected scalar, but got array.
     */
    public function throwIfDbDriverNotScalar()
    {
        $config = array('fp_open_id' => array(
            'db_driver' => array()
        ));

        $this->processConfiguration($config);
    }

    /**
     * @test
     */
    public function shouldAllowToSetIdentityClass()
    {
        $config = array('fp_open_id' => array(
            'identity_class' => 'foo'
        ));

        $this->processConfiguration($config);
    }

    /**
     * @test
     *
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidTypeException
     * @expectedExceptionMessage Invalid type for path "fp_open_id.identity_class". Expected scalar, but got array.
     */
    public function throwIfIdentityClassNotScalar()
    {
        $config = array('fp_open_id' => array(
            'identity_class' => array()
        ));

        $this->processConfiguration($config);
    }

    protected function processConfiguration(array $configs)
    {
        $configuration = new Configuration();
        $processor = new Processor();

        $processor->processConfiguration($configuration, $configs);
    }
}