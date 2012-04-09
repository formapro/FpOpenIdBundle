<?php
namespace Fp\OpenIdBundle\Tests\Security\Core\Exception;

use Fp\OpenIdBundle\Security\Core\Exception\UsernameByIdentityNotFoundException;

class UsernameByIdentityNotFoundExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldBeSubClassOfUsernameNotFoundException()
    {
        $rc = new \ReflectionClass('Fp\OpenIdBundle\Security\Core\Exception\UsernameByIdentityNotFoundException');

        $this->assertTrue($rc->isSubclassOf('Symfony\Component\Security\Core\Exception\UsernameNotFoundException'));
    }

    /**
     * @test
     */
    public function couldBeConstructedWithMessageAsArgument()
    {
        new UsernameByIdentityNotFoundException('user not found');
    }

    /**
     * @test
     */
    public function shouldAllowSetIdentity()
    {
        $exception = new UsernameByIdentityNotFoundException('user not found');

        $exception->setIdentity('an_identity');
    }

    /**
     * @test
     */
    public function shouldAllowGetPreviouslySetIdentity()
    {
        $expectedIdentity = 'the_identity';

        $exception = new UsernameByIdentityNotFoundException('user not found');

        $exception->setIdentity($expectedIdentity);

        $this->assertEquals($expectedIdentity, $exception->getIdentity());
    }

    /**
     * @test
     */
    public function shouldGetEmptyStringAsDefaultIdentity()
    {
        $exception = new UsernameByIdentityNotFoundException('user not found');

        $this->assertSame('', $exception->getIdentity());
    }

    /**
     * @test
     */
    public function shouldAllowSetAttributes()
    {
        $exception = new UsernameByIdentityNotFoundException('user not found');

        $exception->setAttributes(array());
    }

    /**
     * @test
     */
    public function shouldAllowGetPreviouslySetAttributes()
    {
        $expectedAttributes = array(
            'foo' => 'foo',
        );

        $exception = new UsernameByIdentityNotFoundException('user not found');

        $exception->setAttributes($expectedAttributes);

        $this->assertEquals($expectedAttributes, $exception->getAttributes());
    }

    /**
     * @test
     */
    public function shouldGetEmptyArrayAsDefaultAttributes()
    {
        $exception = new UsernameByIdentityNotFoundException('user not found');

        $this->assertSame(array(), $exception->getAttributes());
    }

    /**
     * @test
     */
    public function shouldSerializeIdentity()
    {
        $expectedAttributes = array(
            'foo' => 'foo',
        );

        $exception = new UsernameByIdentityNotFoundException('user not found');

        $exception->setAttributes($expectedAttributes);

        $actualException = unserialize(serialize($exception));

        $this->assertEquals($expectedAttributes, $actualException->getAttributes());
    }

    /**
     * @test
     */
    public function shouldSerializeAttributes()
    {
        $expectedIdentity = 'the_identity';

        $exception = new UsernameByIdentityNotFoundException('user not found');

        $exception->setIdentity($expectedIdentity);

        $actualException = unserialize(serialize($exception));

        $this->assertEquals($expectedIdentity, $actualException->getIdentity());
    }
}