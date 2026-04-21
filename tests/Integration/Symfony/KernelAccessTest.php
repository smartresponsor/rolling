<?php

declare(strict_types=1);

namespace Tests\Integration\Symfony;

use App\Rolling\Controller\V2\AccessController;
use App\Rolling\Infrastructure\Symfony\DependencyInjection\RoleExtension;
use App\Rolling\Infrastructure\Symfony\EventSubscriber\HmacGuardSubscriber;
use App\Rolling\Infrastructure\Symfony\RoleBundle;
use PHPUnit\Framework\TestCase;

final class KernelAccessTest extends TestCase
{
    /**
     * @return void
     */
    public function testBundleClassesExist(): void
    {
        $this->assertTrue(class_exists(RoleBundle::class));
        $this->assertTrue(class_exists(AccessController::class));
        $this->assertTrue(class_exists(HmacGuardSubscriber::class));
        $this->assertTrue(class_exists(RoleExtension::class));
    }
}
