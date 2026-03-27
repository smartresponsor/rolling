<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Infra\Role\Admin;

use Admin\Dto\ApprovalRequest;
use App\InfraInterface\Role\Admin\ApprovalRequestRepositoryInterface;

/**
 *
 */

/**
 *
 */
final class InMemoryApprovalRequestRepository implements ApprovalRequestRepositoryInterface
{
    /** @var array */
    private array $map = [];

    /**
     * @param \Admin\Dto\ApprovalRequest $req
     * @return void
     */
    public function save(ApprovalRequest $req): void
    {
        $this->map[$req->id] = $req;
    }

    /**
     * @param string $id
     * @return \Admin\Dto\ApprovalRequest|null
     */
    public function get(string $id): ?ApprovalRequest
    {
        return $this->map[$id] ?? null;
    }

    /**
     * @return array|\Admin\Dto\ApprovalRequest[]
     */
    public function listPending(): array
    {
        return array_values(array_filter($this->map, fn($r) => $r->status === ApprovalRequest::STATUS_PENDING));
    }
}
