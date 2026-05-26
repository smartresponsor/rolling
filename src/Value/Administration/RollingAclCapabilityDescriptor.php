<?php

declare(strict_types=1);

namespace App\Rolling\Value\Administration;

/**
 * Metadata-only descriptor for a Rolling ACL administration capability.
 */
final readonly class RollingAclCapabilityDescriptor
{
    /** @param array<string, mixed> $context */
    public function __construct(
        private string $key,
        private string $label,
        private string $category,
        private string $status,
        private bool $sensitive,
        private bool $mutation,
        private bool $requiresReview,
        private array $context = [],
    ) {
    }

    public function key(): string
    {
        return $this->key;
    }

    public function category(): string
    {
        return $this->category;
    }

    public function status(): string
    {
        return $this->status;
    }

    public function mutation(): bool
    {
        return $this->mutation;
    }

    /** @return array<string, mixed> */
    public function toSafeArray(): array
    {
        return [
            'key' => $this->key,
            'label' => $this->label,
            'category' => $this->category,
            'status' => $this->status,
            'sensitive' => $this->sensitive,
            'mutation' => $this->mutation,
            'requiresReview' => $this->requiresReview,
            'context' => $this->context,
        ];
    }
}
