<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Admin;

use App\Rolling\DTO\Admin\AdminApprovalRequestDTO;

interface ApprovalWorkflowInterface
{
    public function create(string $requestedBy, string $subjectId, string $role, ?string $tenant = null): AdminApprovalRequestDTO;

    public function approve(string $requestId, string $approverId): AdminApprovalRequestDTO;

    public function reject(string $requestId, string $approverId, string $reason): AdminApprovalRequestDTO;

    public function get(string $requestId): ?AdminApprovalRequestDTO;
}
