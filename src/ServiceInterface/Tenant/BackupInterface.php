<?php
declare(strict_types=1);

namespace App\ServiceInterface\Tenant;

interface BackupInterface
{
    /** @return array{ok:bool, path:string} */
    public function run(string $tenant): array;
}
