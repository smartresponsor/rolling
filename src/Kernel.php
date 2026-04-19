<?php

declare(strict_types=1);

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

final class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $confDir = $this->getProjectDir().'/config';
        $loader->load($confDir.'/packages/*.{php,yaml}', 'glob');

        $envPackagesDir = $confDir.'/packages/'.$this->environment;
        if (is_dir($envPackagesDir)) {
            $loader->load($envPackagesDir.'/**/*.{php,yaml}', 'glob');
        }

        $loader->load($confDir.'/services.yaml');

        $envServices = $confDir.'/services_'.$this->environment.'.yaml';
        if (is_file($envServices)) {
            $loader->load($envServices);
        }
    }

    protected function configureRoutes($routes): void
    {
        $confDir = $this->getProjectDir().'/config';
        $routes->import($confDir.'/routes.yaml');
    }
}
