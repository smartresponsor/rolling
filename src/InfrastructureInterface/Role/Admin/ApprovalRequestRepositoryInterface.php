<?php
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\InfraInterface\Role\Admin;

use Admin\Dto\ApprovalRequest;

/**
 *
 */

/**
 *
 */
interface ApprovalRequestRepositoryInterface
{
    /**
     * @param \Admin\Dto\ApprovalRequest $req
     * @return void
     */
    public function save(ApprovalRequest $req): void;

    /**
     * @param string $id
     * @return \Admin\Dto\ApprovalRequest|null
     */
    public function get(string $id): ?ApprovalRequest;

    /** @return ApprovalRequest[] */
    public function listPending(): array;
}
