<?php
declare(strict_types=1);

namespace Tests\Integration\Symfony;

use App\Integration\Symfony\Controller\RoleApiV2Controller;
use App\Integration\Symfony\DependencyInjection\RoleExtension;
use App\Integration\Symfony\EventSubscriber\HmacGuardSubscriber;
use App\Integration\Symfony\RoleBundle\RoleBundle;
use PHPUnit\Framework\TestCase;

/**
 *
 */

/**
 *
 */
final class KernelAccessTest extends TestCase
{
    /**
     * @return void
     */
    public function testBundleClassesExist(): void
    {
        $this->assertTrue(class_exists(RoleBundle::class));
        $this->assertTrue(class_exists(RoleApiV2Controller::class));
        $this->assertTrue(class_exists(HmacGuardSubscriber::class));
        $this->assertTrue(class_exists(RoleExtension::class));
    }
}
