<?php

declare(strict_types=1);

namespace App\Rolling\Value\Administration;

/**
 * Safe readiness view for Rolling ACL persistence capabilities.
 */
final readonly class RollingAclStorageReadinessReport
{
    /**
     * @param list<string> $expectedEntities
     * @param list<string> $readyCapabilities
     * @param list<string> $pendingCapabilities
     */
    public function __construct(
        private string $storageMode,
        private bool $doctrineBacked,
        private array $expectedEntities,
        private array $readyCapabilities,
        private array $pendingCapabilities,
    ) {
    }

    public function storageMode(): string
    {
        return $this->storageMode;
    }

    public function doctrineBacked(): bool
    {
        return $this->doctrineBacked;
    }

    /** @return list<string> */
    public function expectedEntities(): array
    {
        return $this->expectedEntities;
    }

    /** @return list<string> */
    public function readyCapabilities(): array
    {
        return $this->readyCapabilities;
    }

    /** @return list<string> */
    public function pendingCapabilities(): array
    {
        return $this->pendingCapabilities;
    }

    /** @return array<string, mixed> */
    public function toSafeArray(): array
    {
        return [
            'storageMode' => $this->storageMode,
            'doctrineBacked' => $this->doctrineBacked,
            'expectedEntities' => $this->expectedEntities,
            'readyCapabilities' => $this->readyCapabilities,
            'pendingCapabilities' => $this->pendingCapabilities,
        ];
    }
}
