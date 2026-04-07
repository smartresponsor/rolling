<?php
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\ServiceInterface\Admin;

use App\Service\Admin\Dto\ApprovalRequest;

/**
 *
 */

/**
 *
 */
interface ApprovalWorkflowInterface
{
    /**
     * @param string $requestedBy
     * @param string $subjectId
     * @param string $role
     * @param string|null $tenant
     * @return \Admin\Dto\ApprovalRequest
     */
    public function create(string $requestedBy, string $subjectId, string $role, ?string $tenant = null): ApprovalRequest;

    /**
     * @param string $requestId
     * @param string $approverId
     * @return \Admin\Dto\ApprovalRequest
     */
    public function approve(string $requestId, string $approverId): ApprovalRequest;

    /**
     * @param string $requestId
     * @param string $approverId
     * @param string $reason
     * @return \Admin\Dto\ApprovalRequest
     */
    public function reject(string $requestId, string $approverId, string $reason): ApprovalRequest;

    /**
     * @param string $requestId
     * @return \Admin\Dto\ApprovalRequest|null
     */
    public function get(string $requestId): ?ApprovalRequest;
}
