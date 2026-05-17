<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Admin;

use App\Rolling\Service\Admin\Dto\AdminApprovalRequestDto;

interface ApprovalWorkflowInterface
{
    public function create(string $requestedBy, string $subjectId, string $role, ?string $tenant = null): AdminApprovalRequestDto;

    public function approve(string $requestId, string $approverId): AdminApprovalRequestDto;

    public function reject(string $requestId, string $approverId, string $reason): AdminApprovalRequestDto;

    public function get(string $requestId): ?AdminApprovalRequestDto;
}
