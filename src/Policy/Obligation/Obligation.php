<?php

declare(strict_types=1);

namespace App\Rolling\Policy\Obligation;

final class Obligation
{
    public function __construct(public string $type, public array $params = [])
    {
    }
}
