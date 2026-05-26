<?php

declare(strict_types=1);

namespace App\Rolling\Value\Administration;

/**
 * Safe contract matrix for Rolling ACL administration integration.
 */
final readonly class RollingAclAdministrationContractMatrix
{
    /**
     * @param list<RollingAclAdministrationContractDescriptor> $contracts
     * @param list<string>                                     $guards
     */
    public function __construct(
        private \DateTimeImmutable $generatedAt,
        private array $contracts,
        private array $guards = [],
    ) {
    }

    /** @return list<RollingAclAdministrationContractDescriptor> */
    public function contracts(): array
    {
        return $this->contracts;
    }

    /** @return array<string, int> */
    private function countByStatus(): array
    {
        $counts = [];
        foreach ($this->contracts as $contract) {
            $status = $contract->status();
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
                'totalContracts' => count($this->contracts),
                'requiredContracts' => count(array_filter(
                    $this->contracts,
                    static fn (RollingAclAdministrationContractDescriptor $contract): bool => $contract->required(),
                )),
                'sensitiveContracts' => count(array_filter(
                    $this->contracts,
                    static fn (RollingAclAdministrationContractDescriptor $contract): bool => $contract->sensitive(),
                )),
                'byStatus' => $this->countByStatus(),
            ],
            'contracts' => array_map(
                static fn (RollingAclAdministrationContractDescriptor $contract): array => $contract->toSafeArray(),
                $this->contracts,
            ),
            'guards' => $this->guards,
        ];
    }
}
