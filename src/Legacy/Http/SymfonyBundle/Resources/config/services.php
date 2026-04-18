<?php

declare(strict_types=1);
<<<<<<< HEAD:src/Legacy/Http/SymfonyBundle/Resources/config/services.php
=======

use Http\Role\Client;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa:Http/Role/Role/SymfonyBundle/Resources/config/services.php

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
