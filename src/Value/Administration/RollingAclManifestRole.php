<?php

declare(strict_types=1);

namespace App\Rolling\Value\Administration;

/**
 * Safe role metadata exported by Rolling for Administering visualization.
 */
final class RollingAclManifestRole
{
    /** @param list<string> $inherits */
    public function __construct(
        private readonly string $key,
        private readonly string $label,
        private readonly array $inherits = [],
        private readonly bool $system = false,
    ) {
    }

    public function key(): string
    {
        return $this->key;
    }

    public function label(): string
    {
        return $this->label;
    }

    /** @return list<string> */
    public function inherits(): array
    {
        return $this->inherits;
    }

    public function system(): bool
    {
        return $this->system;
    }

    /** @return array{key: string, label: string, inherits: list<string>, system: bool} */
    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'label' => $this->label,
            'inherits' => $this->inherits,
            'system' => $this->system,
        ];
    }
}
