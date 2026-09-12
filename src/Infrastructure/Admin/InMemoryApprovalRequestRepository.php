<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\Infrastructure\Admin;

use App\Rolling\DTO\Admin\AdminApprovalRequestDTO;
use App\Rolling\InfrastructureInterface\Admin\ApprovalRequestRepositoryInterface;

final class InMemoryApprovalRequestRepository implements ApprovalRequestRepositoryInterface
{
    /** @var array<string, AdminApprovalRequestDTO> */
    private array $map = [];

    public function save(AdminApprovalRequestDTO $req): void
    {
        $this->map[$req->id] = $req;
    }

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
