<?php

declare(strict_types=1);

namespace App\Legacy\Http\SymfonyBundle\Resources\Config;

use App\Legacy\Http\Client;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();
    $services->set(Client::class)
        ->args([
            '%role.endpoint%',
            '%role.hmac_key%',
            '%role.timeout_ms%',
        ]);
};
