<?php

declare(strict_types=1);

namespace App\Rolling\Policy\Role\Obligation\Rules;

use App\Rolling\Entity\Role\PermissionKey;
use App\Rolling\Policy\Obligation\Obligation;

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
