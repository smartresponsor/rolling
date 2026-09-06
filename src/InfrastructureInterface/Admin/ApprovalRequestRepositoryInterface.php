<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\InfrastructureInterface\Admin;

use App\Rolling\DTO\Admin\AdminApprovalRequestDTO;

interface ApprovalRequestRepositoryInterface
{
    public function save(AdminApprovalRequestDTO $req): void;

    public function get(string $id): ?AdminApprovalRequestDTO;

    /** @return list<AdminApprovalRequestDTO> */
    public function listPending(): array;
}
