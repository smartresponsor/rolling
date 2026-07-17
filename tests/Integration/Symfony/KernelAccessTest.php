<?php

declare(strict_types=1);

namespace App\Rolling\Tests\Integration\Symfony;

use App\Rolling\Infrastructure\Symfony\DependencyInjection\RoleExtension;
use App\Rolling\Infrastructure\Symfony\RoleBundle;
use App\Rolling\Service\Cruding\RollingCrudResourceDefinitionProvider;
use App\Rolling\Service\Http\Role\HealthHttpService;
use App\Rolling\ServiceInterface\Cruding\RollingCrudResourceDefinitionProviderInterface;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouteCollection;

final class KernelAccessTest extends TestCase
{
    public function testBundleExtensionAndHostKernelCompile(): void
    {
        self::assertTrue(class_exists(RoleBundle::class));
        self::assertTrue(class_exists(RoleExtension::class));

        $kernel = new RollingHostSmokeKernel('test', true);
        $kernel->boot();

        try {
            $container = $kernel->getContainer();
            self::assertTrue($container->has(RollingCrudResourceDefinitionProviderInterface::class));
            self::assertInstanceOf(
                RollingCrudResourceDefinitionProvider::class,
                $container->get(RollingCrudResourceDefinitionProviderInterface::class),
            );
            self::assertTrue($container->has(HealthHttpService::class));
        } finally {
            $kernel->shutdown();
            self::restoreSymfonyHandlers();
        }
    }

    public function testHostRouteResourcesAreDiscoverable(): void
    {
        $kernel = new RollingHostSmokeKernel('test', true);
        $kernel->boot();

        try {
            $routes = $kernel->loadRollingRoutes();
            self::assertGreaterThan(0, $routes->count());
            self::assertNotNull($routes->get('role_access_check'));
        } finally {
            $kernel->shutdown();
            self::restoreSymfonyHandlers();
        }
    }

    private static function restoreSymfonyHandlers(): void
    {
        restore_exception_handler();
        restore_error_handler();
    }
}

final class RollingHostSmokeKernel extends Kernel
{
    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new DoctrineBundle(),
            new TwigBundle(),
            new RoleBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(static function (ContainerBuilder $container): void {
            $container->loadFromExtension('framework', [
                'test' => true,
                'secret' => 'rolling-host-smoke',
                'router' => [
                    'utf8' => true,
                    'resource' => '%kernel.project_dir%/config/routes.yaml',
                ],
                'http_method_override' => false,
                'handle_all_throwables' => true,
            ]);

            $container->loadFromExtension('doctrine', [
                'dbal' => [
                    'driver' => 'pdo_sqlite',
                    'memory' => true,
                ],
                'orm' => [
                    'auto_mapping' => false,
                    'mappings' => [
                        'Rolling' => [
                            'is_bundle' => false,
                            'type' => 'attribute',
                            'dir' => '%kernel.project_dir%/src/Entity',
                            'prefix' => 'App\\Rolling\\Entity',
                            'alias' => 'Rolling',
                        ],
                    ],
                ],
            ]);

            $container->loadFromExtension('twig', [
                'strict_variables' => true,
                'paths' => [],
            ]);
        });
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir().'/rolling-host-smoke/cache/'.$this->environment.'/'.getmypid();
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir().'/rolling-host-smoke/log/'.getmypid();
    }

    public function getProjectDir(): string
    {
        return dirname(__DIR__, 3);
    }

    public function loadRollingRoutes(): RouteCollection
    {
        /** @var RouteCollection $routes */
        $routes = $this->getContainer()->get('router')->getRouteCollection();

        return $routes;
    }
}
