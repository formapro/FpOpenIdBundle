<?php
namespace Fp\OpenIdBundle\Tests\Model;

use Fp\OpenIdBundle\Model\Identity;

class IdentityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldImplementIdentityInterface()
    {
        $rc = new \ReflectionClass('Fp\OpenIdBundle\Model\Identity');

        $this->assertTrue($rc->implementsInterface('Fp\OpenIdBundle\Model\IdentityInterface'));
    }

    /**
     * @test
     */
    public function couldBeConstructedWithoutAnyArguments()
    {
        new Identity();
    }

    /**
     * @test
     */
    public function shouldAllowGetDefaultAttributesSetInConstructor()
    {
        $identity = new Identity();

        $this->assertEquals(array(), $identity->getAttributes());
    }

    /**
     * @test
     */
    public function shouldAllowSetAttributes()
    {
        $identity = new Identity();

        $identity->setAttributes(array('foo' => 'foo'));
    }

    /**
     * @test
     */
    public function shouldAllowGetAttributesPreviouslySet()
    {
        $expectedAttributes = array('foo' => 'foo');

        $identity = new Identity();

        $identity->setAttributes($expectedAttributes);

        $this->assertEquals($expectedAttributes, $identity->getAttributes());
    }

    /**
     * @test
     */
    public function shouldAllowGetDefaultIdentity()
    {
        $identity = new Identity();

        $this->assertNull($identity->getIdentity());
    }

    /**
     * @test
     */
    public function shouldAllowSetIdentity()
    {
        $identity = new Identity();

        $identity->setIdentity('an_identity');
    }

    /**
     * @test
     */
    public function shouldAllowGetIdentityPreviouslySet()
    {
        $expectedIdentity = 'the_identity';

        $identity = new Identity();

        $identity->setIdentity($expectedIdentity);

        $this->assertEquals($expectedIdentity, $identity->getIdentity());
    }

    /**
     * @test
     */
    public function shouldAllowGetDefaultId()
    {
        $expectedIdentity = 'the_identity';

        $identity = new Identity();

        $this->assertNull($identity->getId());
    }

    /**
     * @test
     */
    public function shouldAllowGetIdPreviouslySet()
    {
        $expectedId = 'the_id';

        $identity = new Identity();

        $ro = new \ReflectionObject($identity);
        $rp = $ro->getProperty('id');
        $rp->setAccessible(true);
        $rp->setValue($identity, $expectedId);
        $rp->setAccessible(false);

        $this->assertEquals($expectedId, $identity->getId());
    }
}