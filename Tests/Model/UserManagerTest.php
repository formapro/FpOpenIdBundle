<?php
namespace Fp\OpenIdBundle\Tests\Model;

use Fp\OpenIdBundle\Model\UserManager;

class UserManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldImplementUserProviderInterface()
    {
        $rc = new \ReflectionClass('Fp\OpenIdBundle\Model\UserManager');

        $this->assertTrue($rc->implementsInterface('Fp\OpenIdBundle\Security\Core\User\UserManagerInterface'));
    }

    /**
     * @test
     */
    public function couldBeConstructedWithIdentityManagerAsArgument()
    {
        new UserManager($this->createIdentityManagerMock());
    }

    /**
     *@test
     */
    public function shouldNotSupportAnyOfUserClass()
    {
        $manager = new UserManager($this->createIdentityManagerMock());

        $this->assertFalse($manager->supportsClass('stdClass'));
        $this->assertFalse($manager->supportsClass('Symfony\Component\Security\Core\User\UserInterface'));
        $this->assertFalse($manager->supportsClass('Symfony\Component\Security\Core\User\AdvancedUserInterface'));
        $this->assertFalse($manager->supportsClass('Symfony\Component\Security\Core\User\User'));
    }

    /**
     * @test
     *
     * @expectedException Symfony\Component\Security\Core\Exception\UnsupportedUserException
     * @expectedExceptionMessage The provider cannot refresh any kind of user.
     */
    public function throwAlwaysWhenRefreshUserMethodCalled()
    {
        $manager = new UserManager($this->createIdentityManagerMock());

        $manager->refreshUser($this->createUserMock());
    }

    /**
     *@test
     *
     * @expectedException Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     * @expectedExceptionMessage Identity the_identity not found.
     */
    public function throwUsernameNotFoundIfIdentityManagerNotFindIdentity()
    {
        $identityManagerMock = $this->createIdentityManagerMock();
        $identityManagerMock
            ->expects($this->once())
            ->method('findByIdentity')
            ->will($this->returnValue(null))
        ;

        $manager = new UserManager($identityManagerMock);

        $manager->loadUserByUsername('the_identity');
    }

    /**
     *@test
     *
     * @expectedException Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     * @expectedExceptionMessage Identity must implement UserIdentityInterface.
     */
    public function throwUsernameNotFoundIfFoundIdentityNotImplementUserIdentityInterface()
    {
        $expectedIdentity = 'the_identity';

        $identityManagerMock = $this->createIdentityManagerMock();
        $identityManagerMock
            ->expects($this->once())
            ->method('findByIdentity')
            ->with($expectedIdentity)
            ->will($this->returnValue($this->createIdentityMock()))
        ;

        $manager = new UserManager($identityManagerMock);

        $manager->loadUserByUsername($expectedIdentity);
    }

    /**
     *@test
     *
     * @expectedException Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     * @expectedExceptionMessage UserIdentity must have a user to be set previously.
     */
    public function throwUsernameNotFoundIfFoundUserIdentityNotHaveUserSet()
    {
        $expectedIdentity = 'the_identity';

        $identityManagerMock = $this->createIdentityManagerMock();
        $identityManagerMock
            ->expects($this->once())
            ->method('findByIdentity')
            ->with($expectedIdentity)
            ->will($this->returnValue($this->createUserIdentityMock()))
        ;

        $manager = new UserManager($identityManagerMock);

        $manager->loadUserByUsername($expectedIdentity);
    }

    /**
     * @test
     *
     * @expectedException Symfony\Component\Security\Core\Exception\AuthenticationServiceException
     * @expectedExceptionMessage The manager does not implement createUserFromIdentity method. Please extend manager and overwrite the method with your logic.
     */
    public function throwAlwaysWhenCreateUserFromIdentityMethodCalled()
    {
        $manager = new UserManager($this->createIdentityManagerMock());

        $manager->createUserFromIdentity('an_identity');
    }

    protected function createIdentityManagerMock()
    {
        return $this->getMock('Fp\OpenIdBundle\Model\IdentityManagerInterface');
    }

    protected function createUserMock()
    {
        return $this->getMock('Symfony\Component\Security\Core\User\UserInterface');
    }

    protected function createIdentityMock()
    {
        return $this->getMock('Fp\OpenIdBundle\Model\IdentityInterface');
    }

    protected function createUserIdentityMock()
    {
        return $this->getMock('Fp\OpenIdBundle\Model\UserIdentityInterface');
    }
}