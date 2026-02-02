<?php

use Http\Role\Client;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;

return static function (ContainerConfigurator $configurator) {
    $services = $configurator->services();
    $services->set(Client::class)
        ->args([
            '%role.endpoint%',
            '%role.hmac_key%',
            '%role.timeout_ms%',
        ]);
};
