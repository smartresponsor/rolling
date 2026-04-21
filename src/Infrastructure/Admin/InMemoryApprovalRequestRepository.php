<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\Infrastructure\Admin;

use App\Rolling\InfrastructureInterface\Admin\ApprovalRequestRepositoryInterface;
use App\Rolling\Service\Admin\Dto\ApprovalRequest;

final class InMemoryApprovalRequestRepository implements ApprovalRequestRepositoryInterface
{
    /** @var array<string, ApprovalRequest> */
    private array $map = [];

    public function save(ApprovalRequest $req): void
    {
        $this->map[$req->id] = $req;
    }

    public function get(string $id): ?ApprovalRequest
    {
        return $this->map[$id] ?? null;
    }

    /**
     * @return list<ApprovalRequest>
     */
    public function listPending(): array
    {
        return array_values(array_filter($this->map, fn ($r) => ApprovalRequest::STATUS_PENDING === $r->status));
    }
}
