<?php

declare(strict_types=1);

namespace App\Policy\Role\Obligation\Rules;

use App\Entity\Role\PermissionKey;
use App\Policy\Obligation\Obligation;

final class WatermarkRule
{
    public function __construct(private readonly string $header, private readonly string $value)
    {
    }

    public function obligationFor(PermissionKey $action): ?Obligation
    {
        return new Obligation('watermark', ['header' => $this->header, 'value' => $this->value]);
    }
}
