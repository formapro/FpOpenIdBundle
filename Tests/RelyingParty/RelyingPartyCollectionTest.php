<?php
namespace Fp\OpenIdBundle\Tests\RelyingParty;

use Fp\OpenIdBundle\RelyingParty\RelyingPartyCollection;

/**
 * @author Kotlyar Maksim <kotlyar.maksim@gmail.com>
 * @since 4/12/12
 */
class RelyingPartyCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldImplementsRelyingPartyInterface()
    {
        $rc = new \ReflectionClass('Fp\OpenIdBundle\RelyingParty\RelyingPartyCollection');

        $this->assertTrue($rc->implementsInterface('Fp\OpenIdBundle\RelyingParty\RelyingPartyInterface'));
    }

    /**
     * @test
     */
    public function couldBeConstructedWithoutAnyArguments()
    {
        new RelyingPartyCollection;
    }

    /**
     * @test
     */
    public function shouldAllowPrependRelyingParty()
    {
        $collection = new RelyingPartyCollection;

        $collection->prepend($this->createRelyingPartyMock());
    }

    /**
     * @test
     */
    public function shouldAllowAppendRelyingParty()
    {
        $collection = new RelyingPartyCollection;

        $collection->append($this->createRelyingPartyMock());
    }

    /**
     * @test
     */
    public function shouldNotSupportIfCollectionEmpty()
    {
        $collection = new RelyingPartyCollection;

        $this->assertFalse($collection->supports($this->createRequestMock()));
    }

    /**
     * @test
     */
    public function shouldNotSupportIfAnyRelyingPartyInCollectionNotSupport()
    {
        $relyingPartyOneMock = $this->createRelyingPartyMock();
        $relyingPartyOneMock
            ->expects($this->once())
            ->method('supports')
            ->will($this->returnValue(false))
        ;

        $relyingPartyTwoMock = $this->createRelyingPartyMock();
        $relyingPartyTwoMock
            ->expects($this->once())
            ->method('supports')
            ->will($this->returnValue(false))
        ;

        $collection = new RelyingPartyCollection;

        $collection->append($relyingPartyOneMock);
        $collection->prepend($relyingPartyTwoMock);

        $this->assertFalse($collection->supports($this->createRequestMock()));
    }

    /**
     * @test
     */
    public function shouldSupportIfRelyingPartyInCollectionSupport()
    {
        $relyingPartyOneMock = $this->createRelyingPartyMock();
        $relyingPartyOneMock
            ->expects($this->once())
            ->method('supports')
            ->will($this->returnValue(false))
        ;

        $relyingPartyTwoMock = $this->createRelyingPartyMock();
        $relyingPartyTwoMock
            ->expects($this->once())
            ->method('supports')
            ->will($this->returnValue(true))
        ;

        $collection = new RelyingPartyCollection;

        $collection->append($relyingPartyOneMock);
        $collection->append($relyingPartyTwoMock);

        $this->assertTrue($collection->supports($this->createRequestMock()));
    }

    /**
     * @test
     */
    public function shouldStopOnFirstSupportedRelyingPartyWhileCheckingWhetherCollectionSupportOrNot()
    {
        $relyingPartyOneMock = $this->createRelyingPartyMock();
        $relyingPartyOneMock
            ->expects($this->once())
            ->method('supports')
            ->will($this->returnValue(true))
        ;

        $relyingPartyTwoMock = $this->createRelyingPartyMock();
        $relyingPartyTwoMock
            ->expects($this->never())
            ->method('supports')
        ;

        $collection = new RelyingPartyCollection;

        $collection->append($relyingPartyOneMock);
        $collection->append($relyingPartyTwoMock);

        $this->assertTrue($collection->supports($this->createRequestMock()));
    }

    /**
     * @test
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The relying party does not support the request
     */
    public function throwIfTryManageEmptyCollection()
    {
        $collection = new RelyingPartyCollection;

        //guard
        $this->assertFalse($collection->supports($this->createRequestMock()));

        $collection->manage($this->createRequestMock());
    }

    /**
     * @test
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The relying party does not support the request
     */
    public function throwIfTryManageRequestNotSupportedByAnyRelyingPartyInCollection()
    {
        $relyingPartyOneMock = $this->createRelyingPartyMock();
        $relyingPartyOneMock
            ->expects($this->atLeastOnce())
            ->method('supports')
            ->will($this->returnValue(false))
        ;

        $relyingPartyTwoMock = $this->createRelyingPartyMock();
        $relyingPartyTwoMock
            ->expects($this->atLeastOnce())
            ->method('supports')
            ->will($this->returnValue(false))
        ;

        $collection = new RelyingPartyCollection;

        $collection->append($relyingPartyOneMock);
        $collection->append($relyingPartyTwoMock);

        //guard
        $this->assertFalse($collection->supports($this->createRequestMock()));

        $collection->manage($this->createRequestMock());
    }

    /**
     * @test
     */
    public function shouldProxyManagingToRelyingPartyWhichSupportRequest()
    {
        $expectedRequest = $this->createRequestMock();

        $relyingPartyOneMock = $this->createRelyingPartyMock();
        $relyingPartyOneMock
            ->expects($this->any())
            ->method('supports')
            ->will($this->returnValue(true))
        ;
        $relyingPartyOneMock
            ->expects($this->once())
            ->method('manage')
            ->with($expectedRequest)
        ;

        $collection = new RelyingPartyCollection;

        $collection->append($relyingPartyOneMock);

        $collection->manage($expectedRequest);
    }

    /**
     * @test
     */
    public function shouldReturnResultOfRelyingPartyWhichSupportRequestOnManaging()
    {
        $expectedResult = 'the_relying_party_result';

        $relyingPartyOneMock = $this->createRelyingPartyMock();
        $relyingPartyOneMock
            ->expects($this->any())
            ->method('supports')
            ->will($this->returnValue(true))
        ;
        $relyingPartyOneMock
            ->expects($this->once())
            ->method('manage')
            ->will($this->returnValue($expectedResult))
        ;

        $collection = new RelyingPartyCollection;

        $collection->append($relyingPartyOneMock);

        $this->assertSame($expectedResult, $collection->manage($this->createRequestMock()));
    }

    public function createRequestMock()
    {
        return $this->getMock('Symfony\Component\HttpFoundation\Request', array(), array(), '', false, false);
    }

    public function createRelyingPartyMock()
    {
        return $this->getMock('Fp\OpenIdBundle\RelyingParty\RelyingPartyInterface');
    }
}
