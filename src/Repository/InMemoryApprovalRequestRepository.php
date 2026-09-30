<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\Repository;

use App\Rolling\DTO\Admin\AdminApprovalRequestDTO;
use App\Rolling\InfrastructureInterface\Admin\ApprovalRequestRepositoryInterface;

/** Repository implementation for InMemoryApprovalRequestRepository. */
final class InMemoryApprovalRequestRepository implements ApprovalRequestRepositoryInterface
{
    /** @var array<string, AdminApprovalRequestDTO> */
    private array $map = [];

    /** Persists the supplied value in the repository. */
    public function save(AdminApprovalRequestDTO $req): void
    {
        $this->map[$req->id] = $req;
    }

    /** Returns the repository value for the supplied identifier. */
    public function get(string $id): ?AdminApprovalRequestDTO
    {
        return $this->map[$id] ?? null;
    }

    /**
     * @return list<AdminApprovalRequestDTO>
     */
    public function listPending(): array
    {
        return array_values(array_filter($this->map, fn ($r) => AdminApprovalRequestDTO::STATUS_PENDING === $r->status));
    }
}
