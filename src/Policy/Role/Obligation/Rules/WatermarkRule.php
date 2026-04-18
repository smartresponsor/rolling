<?php

declare(strict_types=1);

namespace App\Policy\Role\Obligation\Rules;

use Policy\Role\Obligation\Obligation;
use src\Entity\Role\PermissionKey;

final class WatermarkRule
{
    public function __construct(private readonly string $header, private readonly string $value) {}

    public function obligationFor(PermissionKey $action): ?Obligation
    {
        return new Obligation('watermark', ['header' => $this->header, 'value' => $this->value]);
    }
}
