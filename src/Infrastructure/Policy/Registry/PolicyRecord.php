<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Policy\Registry;

final class PolicyRecord
{
    public function __construct(
        public string $ns,
        public string $name,
        public string $version,
        public string $docJson,
        public int $createdAt,
        public bool $isActive,
    ) {
    }
}
