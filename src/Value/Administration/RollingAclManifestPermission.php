<?php

declare(strict_types=1);

namespace App\Rolling\Value\Administration;

/**
 * Permission row exported by Rolling for Administering visualization.
 */
final class RollingAclManifestPermission
{
    /** @param list<string> $scopes */
    public function __construct(
        private readonly string $key,
        private readonly string $label,
        private readonly string $category,
        private readonly array $scopes,
        private readonly bool $sensitive,
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

    /** @return array{key: string, label: string, category: string, scopes: list<string>, sensitive: bool} */
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
