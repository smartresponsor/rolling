<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\InfrastructureInterface\Admin;

use App\Service\Admin\Dto\ApprovalRequest;

interface ApprovalRequestRepositoryInterface
{
    public function save(ApprovalRequest $req): void;

    public function get(string $id): ?ApprovalRequest;

    /** @return list<ApprovalRequest> */
    public function listPending(): array;
}
