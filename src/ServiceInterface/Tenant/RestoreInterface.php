<?php
declare(strict_types=1);

namespace App\ServiceInterface\Tenant;

interface RestoreInterface
{
    /** @return array{ok:bool, tuples:int} */
    public function run(string $zipPath): array;
}
