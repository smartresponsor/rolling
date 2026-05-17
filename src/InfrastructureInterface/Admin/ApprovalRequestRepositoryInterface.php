<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\InfrastructureInterface\Admin;

use App\Rolling\Service\Admin\Dto\AdminApprovalRequestDto;

interface ApprovalRequestRepositoryInterface
{
    public function save(AdminApprovalRequestDto $req): void;

    public function get(string $id): ?AdminApprovalRequestDto;

    /** @return list<AdminApprovalRequestDto> */
    public function listPending(): array;
}
