<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Service\Admin;

use App\Service\Admin\Dto\ApprovalRequest;
use App\InfrastructureInterface\Admin\ApprovalRequestRepositoryInterface;
use App\ServiceInterface\Admin\Action\GrantRoleActionInterface;
use App\ServiceInterface\Admin\ApprovalWorkflowInterface;
use App\ServiceInterface\Admin\Guard\ApprovalGuardInterface;
use Exception;
use RuntimeException;

/**
 *
 */

/**
 *
 */
final class ApprovalWorkflow implements ApprovalWorkflowInterface
{
    /**
     * @param \App\InfrastructureInterface\Admin\ApprovalRequestRepositoryInterface $repo
     * @param \App\ServiceInterface\Admin\Guard\ApprovalGuardInterface $guard
     * @param \App\ServiceInterface\Admin\Action\GrantRoleActionInterface $applier
     */
    public function __construct(
        private readonly ApprovalRequestRepositoryInterface $repo,
        private readonly ApprovalGuardInterface             $guard,
        private readonly GrantRoleActionInterface           $applier,
    ) {}

    /**
     * @param string $requestedBy
     * @param string $subjectId
     * @param string $role
     * @param string|null $tenant
     * @return \Admin\Dto\ApprovalRequest
     */
    public function create(string $requestedBy, string $subjectId, string $role, ?string $tenant = null): ApprovalRequest
    {
        $id = self::newId();
        $req = new ApprovalRequest($id, $requestedBy, $subjectId, $role, $tenant);
        $this->repo->save($req);
        return $req;
    }

    /**
     * @param string $requestId
     * @param string $approverId
     * @return \Admin\Dto\ApprovalRequest
     */
    public function approve(string $requestId, string $approverId): ApprovalRequest
    {
        $req = $this->mustGet($requestId);
        if ($req->status !== ApprovalRequest::STATUS_PENDING) {
            return $req;
        }
        $req->addApproval($approverId);
        if ($this->guard->isSatisfied($req)) {
            $req->status = ApprovalRequest::STATUS_APPROVED;
            $this->applier->apply($req);
        }
        $this->repo->save($req);
        return $req;
    }

    /**
     * @param string $requestId
     * @param string $approverId
     * @param string $reason
     * @return \Admin\Dto\ApprovalRequest
     */
    public function reject(string $requestId, string $approverId, string $reason): ApprovalRequest
    {
        $req = $this->mustGet($requestId);
        if ($req->status !== ApprovalRequest::STATUS_PENDING) {
            return $req;
        }
        $req->status = ApprovalRequest::STATUS_REJECTED;
        $req->rejectedBy = $approverId;
        $req->rejectReason = $reason;
        $this->repo->save($req);
        return $req;
    }

    /**
     * @param string $requestId
     * @return \Admin\Dto\ApprovalRequest|null
     */
    public function get(string $requestId): ?ApprovalRequest
    {
        return $this->repo->get($requestId);
    }

    /**
     * @param string $id
     * @return \Admin\Dto\ApprovalRequest
     */
    private function mustGet(string $id): ApprovalRequest
    {
        $req = $this->repo->get($id);
        if (!$req) {
            throw new RuntimeException('Approval request not found: ' . $id);
        }
        return $req;
    }

    /**
     * @return string
     */
    private static function newId(): string
    {
        try {
            return 'apr_' . bin2hex(random_bytes(6));
        } catch (Exception $e) {
            error_log('ApprovalWorkflow::newId fallback: ' . $e->getMessage());

            return 'apr_' . str_replace('.', '', (string) microtime(true));
        }
    }
}
