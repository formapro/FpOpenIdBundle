<?php
namespace Fp\OpenIdBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Fp\OpenIdBundle\DependencyInjection\FpOpenIdExtension;

class FpOpenIdExtensionTest extends \PHPUnit_Framework_TestCase
{
    public static function provideSupportedDbDrivers()
    {
        return array(
            array('orm','mongodb')
        );
    }

    /**
     * @test
     */
    public function shouldLoadExtensionWithEmptyConfig()
    {
        $configs = array();

        $containerBuilder = new ContainerBuilder(new ParameterBag);

        $extension = new FpOpenIdExtension();
        $extension->load($configs, $containerBuilder);
    }

    /**
     * @test
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid db driver "not_supported_db_driver"
     */
    public function throwIfDbDriverNotSupported()
    {
        $config = array('db_driver' => 'not_supported_db_driver');
        $configs = array($config);

        $containerBuilder = new ContainerBuilder(new ParameterBag);

        $extension = new FpOpenIdExtension();
        $extension->load($configs, $containerBuilder);
    }

    /**
     * @test
     *
     * @dataProvider provideSupportedDbDrivers
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage  The option `identity_class` has to be configured to use db_driver
     */
    public function throwIfDbDriverDefinedButIdentityClassNot($supportedDbDriver)
    {
        $config = array('db_driver' => $supportedDbDriver);
        $configs = array($config);

        $containerBuilder = new ContainerBuilder(new ParameterBag);

        $extension = new FpOpenIdExtension();
        $extension->load($configs, $containerBuilder);
    }

    /**
     * @test
     *
     * @dataProvider provideSupportedDbDrivers
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage  The option `identity_class` contains IsNotValidIdentityClass but it is not a valid class name.
     */
    public function throwIfDbDriverDefinedButIdentityClassIsNotValidClass($supportedDbDriver)
    {
        $config = array(
            'db_driver' => $supportedDbDriver,
            'identity_class' => 'IsNotValidIdentityClass',
        );
        $configs = array($config);

        $containerBuilder = new ContainerBuilder(new ParameterBag);

        $extension = new FpOpenIdExtension();
        $extension->load($configs, $containerBuilder);
    }

    /**
     * @test
     *
     * @dataProvider provideSupportedDbDrivers
     */
    public function shouldLoadExtensionWithDbDriverAndIdentityDefined($supportedDbDriver)
    {
        $config = array(
            'db_driver' => $supportedDbDriver,
            'identity_class' => 'stdClass',
        );
        $configs = array($config);

        $containerBuilder = new ContainerBuilder(new ParameterBag);

        $extension = new FpOpenIdExtension();
        $extension->load($configs, $containerBuilder);
    }

    /**
     * @test
     */
    public function shouldAddTemplateEngineParameterToContainerBuilder()
    {
        $expectedTemplateEngine = 'the_engine';

        $config = array(
            'template' => array(
                'engine' => $expectedTemplateEngine
            )
        );
        $configs = array($config);

        $containerBuilder = new ContainerBuilder(new ParameterBag);
        //guard
        $this->assertFalse($containerBuilder->hasParameter('fp_openid.template.engine'));

        $extension = new FpOpenIdExtension();
        $extension->load($configs, $containerBuilder);

        $this->assertTrue($containerBuilder->hasParameter('fp_openid.template.engine'));
        $this->assertEquals($expectedTemplateEngine, $containerBuilder->getParameter('fp_openid.template.engine'));
    }
}