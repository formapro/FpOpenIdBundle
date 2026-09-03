<?php
namespace Fp\OpenIdBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;

/**
 * @author Kotlyar Maksim <kotlyar.maksim@gmail.com>
 * @since 4/27/12
 */
abstract class WebTestCase extends BaseWebTestCase
{
    /**
     * @return string
     */
    public static function getKernelClass()
    {
        require_once __DIR__ . '/AppKernel.php';
        
        return 'AppKernel';
    }
}
