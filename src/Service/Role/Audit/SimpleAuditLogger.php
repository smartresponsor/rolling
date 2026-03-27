<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace Audit;

use App\InfraInterface\Role\Audit\AuditRepositoryInterface;
use App\ServiceInterface\Role\Audit\AuditLoggerInterface;
use Audit\Dto\DecisionRecord;

/**
 *
 */

/**
 *
 */
final class SimpleAuditLogger implements AuditLoggerInterface
{
    /**
     * @param \App\InfraInterface\Role\Audit\AuditRepositoryInterface $repo
     */
    public function __construct(private readonly AuditRepositoryInterface $repo) {}

    /**
     * @param \Audit\Dto\DecisionRecord $rec
     * @return void
     */
    public function log(DecisionRecord $rec): void
    {
        $this->repo->save($rec->toArray());
    }
}
