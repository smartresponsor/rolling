<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\Service\Admin;

use App\Rolling\DTO\Admin\AdminApprovalRequestDTO;
use App\Rolling\InfrastructureInterface\Admin\ApprovalRequestRepositoryInterface;
use App\Rolling\ServiceInterface\Admin\Action\GrantRoleActionInterface;
use App\Rolling\ServiceInterface\Admin\ApprovalWorkflowInterface;
use App\Rolling\ServiceInterface\Admin\Guard\ApprovalGuardInterface;

final class AdminApprovalWorkflowService implements ApprovalWorkflowInterface
{
    public function __construct(
        private readonly ApprovalRequestRepositoryInterface $repo,
        private readonly ApprovalGuardInterface $guard,
        private readonly GrantRoleActionInterface $applier,
    ) {
    }

    public function create(string $requestedBy, string $subjectId, string $role, ?string $tenant = null): AdminApprovalRequestDTO
    {
        $id = self::newId();
        $req = new AdminApprovalRequestDTO($id, $requestedBy, $subjectId, $role, $tenant);
        $this->repo->save($req);

        return $req;
    }

    public function approve(string $requestId, string $approverId): AdminApprovalRequestDTO
    {
        $req = $this->mustGet($requestId);
        if (AdminApprovalRequestDTO::STATUS_PENDING !== $req->status) {
            return $req;
        }
        $req->addApproval($approverId);
        if ($this->guard->isSatisfied($req)) {
            $req->status = AdminApprovalRequestDTO::STATUS_APPROVED;
            $this->applier->apply($req);
        }
        $this->repo->save($req);

        return $req;
    }

    public function reject(string $requestId, string $approverId, string $reason): AdminApprovalRequestDTO
    {
        $req = $this->mustGet($requestId);
        if (AdminApprovalRequestDTO::STATUS_PENDING !== $req->status) {
            return $req;
        }
        $req->status = AdminApprovalRequestDTO::STATUS_REJECTED;
        $req->rejectedBy = $approverId;
        $req->rejectReason = $reason;
        $this->repo->save($req);

        return $req;
    }

    public function get(string $requestId): ?AdminApprovalRequestDTO
    {
        return $this->repo->get($requestId);
    }

    private function mustGet(string $id): AdminApprovalRequestDTO
    {
        $req = $this->repo->get($id);
        if (!$req) {
            throw new \RuntimeException('Approval request not found: '.$id);
        }

        return $req;
    }

    private static function newId(): string
    {
        try {
            return 'apr_'.bin2hex(random_bytes(6));
        } catch (\Exception $e) {
            error_log('AdminApprovalWorkflowService::newId fallback: '.$e->getMessage());

            return 'apr_'.str_replace('.', '', (string) microtime(true));
        }
    }
}
