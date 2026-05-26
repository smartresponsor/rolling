<?php

declare(strict_types=1);

namespace App\Rolling\Value\Administration;

/**
 * Metadata-only capability matrix for Rolling ACL administration.
 */
final readonly class RollingAclCapabilityMatrix
{
    /**
     * @param list<RollingAclCapabilityDescriptor> $capabilities
     * @param list<string>                         $guards
     */
    public function __construct(
        private \DateTimeImmutable $generatedAt,
        private array $capabilities,
        private array $guards = [],
    ) {
    }

    /** @return list<RollingAclCapabilityDescriptor> */
    public function capabilities(): array
    {
        return $this->capabilities;
    }

    /** @return array<string, int> */
    private function countByStatus(): array
    {
        $counts = [];
        foreach ($this->capabilities as $capability) {
            $status = $capability->status();
            $counts[$status] = ($counts[$status] ?? 0) + 1;
        }

        ksort($counts);

        return $counts;
    }

    /** @return array<string, mixed> */
    public function toSafeArray(): array
    {
        return [
            'generatedAt' => $this->generatedAt->format(\DateTimeInterface::ATOM),
            'summary' => [
                'totalCapabilities' => count($this->capabilities),
                'mutationCapabilities' => count(array_filter(
                    $this->capabilities,
                    static fn (RollingAclCapabilityDescriptor $capability): bool => $capability->mutation(),
                )),
                'byStatus' => $this->countByStatus(),
            ],
            'capabilities' => array_map(
                static fn (RollingAclCapabilityDescriptor $capability): array => $capability->toSafeArray(),
                $this->capabilities,
            ),
            'guards' => $this->guards,
        ];
    }
}
