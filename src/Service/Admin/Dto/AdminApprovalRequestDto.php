<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\Service\Admin\Dto;

final class AdminApprovalRequestDto
{
    public const string STATUS_PENDING = 'pending';
    public const string STATUS_APPROVED = 'approved';
    public const string STATUS_REJECTED = 'rejected';

    public function __construct(
        public string $id,
        public string $requestedBy,
        public string $subjectId,
        public string $role,
        public ?string $tenant,
        /** @var string[] */
        public array $approvers = [],
        public string $status = self::STATUS_PENDING,
        public ?string $rejectedBy = null,
        public ?string $rejectReason = null,
        public int $requiredApprovals = 2,
        public int $createdAt = 0,
        public int $updatedAt = 0,
    ) {
        $this->createdAt = $this->createdAt ?: time();
        $this->updatedAt = $this->updatedAt ?: $this->createdAt;
    }

    public function addApproval(string $approverId): void
    {
        if (!in_array($approverId, $this->approvers, true)) {
            $this->approvers[] = $approverId;
            $this->updatedAt = time();
        }
    }
}
