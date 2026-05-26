<?php

declare(strict_types=1);

namespace App\Rolling\Value\Administration;

/**
 * Safe UI/catalog descriptor for a Rolling permission exposed to Administering.
 */
final readonly class RollingAdministrationPermissionDescriptor
{
    /** @param list<string> $scopes */
    public function __construct(
        private string $key,
        private string $label,
        private string $category,
        private array $scopes,
        private bool $sensitive = false,
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

    public function category(): string
    {
        return $this->category;
    }

    /** @return list<string> */
    public function scopes(): array
    {
        return $this->scopes;
    }

    public function sensitive(): bool
    {
        return $this->sensitive;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'label' => $this->label,
            'category' => $this->category,
            'scopes' => $this->scopes,
            'sensitive' => $this->sensitive,
        ];
    }
}
