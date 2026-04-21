<?php

declare(strict_types=1);

namespace App\Rolling\Policy\Obligation;

final class Obligation
{
    /**
     * @param string $type
     * @param array  $params
     */
    public function __construct(public string $type, public array $params = [])
    {
    }
}
