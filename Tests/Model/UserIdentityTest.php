<?php
namespace Fp\OpenIdBundle\Tests\Model;

use Fp\OpenIdBundle\Model\UserIdentity;

class UserIdentityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldImplementUserIdentityInterface()
    {
        $rc = new \ReflectionClass('Fp\OpenIdBundle\Model\UserIdentity');

        $this->assertTrue($rc->implementsInterface('Fp\OpenIdBundle\Model\UserIdentityInterface'));
    }

    /**
     * @test
     */
    public function shouldBeSubClassOfIdentityModel()
    {
        $rc = new \ReflectionClass('Fp\OpenIdBundle\Model\UserIdentity');

        $this->assertTrue($rc->isSubclassOf('Fp\OpenIdBundle\Model\Identity'));
    }

    /**
     * @test
     */
    public function couldBeConstructedWithoutAnyArguments()
    {
        new UserIdentity();
    }

    /**
     * @test
     */
    public function shouldAllowGetPreviouslySetUser()
    {
        $expectedUser = $this->createUser();
        
        $userIdentity = new UserIdentity();
        $userIdentity->setUser($expectedUser);

        $this->assertSame($expectedUser, $userIdentity->getUser());
    }
    
    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\Security\Core\User\UserInterface
     */
    protected function createUser()
    {
        return $this->getMock('Symfony\Component\Security\Core\User\UserInterface');
    }
}