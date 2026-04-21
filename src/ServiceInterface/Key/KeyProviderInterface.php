<?php

declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Key;

interface KeyProviderInterface
{
    /** @return array{kid:string, material:string} */
    public function getActive(string $tenant): array;

    /** @return array{kid:string, material:string}|null */
    public function getById(string $tenant, string $kid): ?array;

    /** @return array{kid:string, material:string} */
    public function rotate(string $tenant): array;
}
