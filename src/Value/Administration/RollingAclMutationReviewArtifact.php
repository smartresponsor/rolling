<?php

declare(strict_types=1);

namespace App\Rolling\Value\Administration;

/**
 * Safe export artifact for an ACL mutation review.
 *
 * This DTO is intended for Administering operation artifacts and diagnostics.
 * It contains only metadata and review summaries.
 */
final readonly class RollingAclMutationReviewArtifact
{
    /** @param array<string, mixed> $payload */
    public function __construct(
        private string $artifactKey,
        private string $checksum,
        private array $payload,
        private \DateTimeImmutable $generatedAt = new \DateTimeImmutable(),
    ) {
    }

    public function artifactKey(): string
    {
        return $this->artifactKey;
    }

    public function checksum(): string
    {
        return $this->checksum;
    }

    /** @return array<string, mixed> */
    public function payload(): array
    {
        return $this->payload;
    }

    public function generatedAt(): \DateTimeImmutable
    {
        return $this->generatedAt;
    }

    /** @return array<string, mixed> */
    public function toSafeArray(): array
    {
        return [
            'artifact_key' => $this->artifactKey,
            'checksum' => $this->checksum,
            'generated_at' => $this->generatedAt->format(\DateTimeInterface::ATOM),
            'payload' => $this->payload,
        ];
    }
}
