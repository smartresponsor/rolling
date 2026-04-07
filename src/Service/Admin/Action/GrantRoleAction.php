<?php
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Service\Admin\Action;

use App\Service\Admin\Dto\ApprovalRequest;
use App\ServiceInterface\Admin\Action\GrantRoleActionInterface;

/**
 *
 */

/**
 *
 */
final class GrantRoleAction implements GrantRoleActionInterface
{
    /**
     * @param string $reportDir
     */
    public function __construct(private readonly string $reportDir)
    {
    }

    /**
     * @param \Admin\Dto\ApprovalRequest $req
     * @return void
     */
    public function apply(ApprovalRequest $req): void
    {
        @mkdir($this->reportDir, 0775, true);
        $path = $this->reportDir . '/grants_applied.ndjson';
        $row = [
            'requestId' => $req->id,
            'subjectId' => $req->subjectId,
            'role' => $req->role,
            'tenant' => $req->tenant,
            'status' => $req->status,
            'approvers' => $req->approvers,
            'ts' => date('c'),
        ];
        file_put_contents($path, json_encode($row) . PHP_EOL, FILE_APPEND);
    }
}
