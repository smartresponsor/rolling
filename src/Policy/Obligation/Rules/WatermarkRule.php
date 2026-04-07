<?php

declare(strict_types=1);

namespace App\Policy\Obligation\Rules;

use App\Policy\Obligation\Obligation;
use App\Entity\Role\PermissionKey;
use App\Entity\Role\Scope;
use App\Entity\Role\SubjectId;

final class WatermarkRule implements RuleInterface
{
    public function __construct(private string $header = 'X-Policy', private string $value = '')
    {
    }

    public function evaluate(SubjectId $subject, PermissionKey $action, Scope $scope, array $context = []): ?Obligation
    {
        return new Obligation('watermark', ['header' => $this->header, 'value' => $this->value]);
    }
}
