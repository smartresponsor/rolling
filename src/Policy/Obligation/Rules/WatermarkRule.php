<?php

declare(strict_types=1);

namespace App\Policy\Obligation\Rules;

use App\Entity\Role\PermissionKey;
use App\Entity\Role\Scope;
use App\Entity\Role\SubjectId;
use App\Policy\Obligation\Obligation;

final class WatermarkRule implements RuleInterface
{
    public function __construct(private readonly string $header = 'X-Policy', private readonly string $value = '')
    {
    }

    public function evaluate(SubjectId $subject, PermissionKey $action, Scope $scope, array $context = []): ?Obligation
    {
        return new Obligation('watermark', ['header' => $this->header, 'value' => $this->value]);
    }
}
