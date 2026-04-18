<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Service\Audit;

use App\InfrastructureInterface\App\Service\Audit\AuditRepositoryInterface;
use App\ServiceInterface\App\Service\Audit\AuditLoggerInterface;
use App\Service\Audit\Dto\DecisionRecord;

/**
 *
 */

/**
 *
 */
final class SimpleAuditLogger implements AuditLoggerInterface
{
    /**
     * @param \App\InfrastructureInterface\App\Service\Audit\AuditRepositoryInterface $repo
     */
    public function __construct(private readonly AuditRepositoryInterface $repo) {}

    /**
     * @param \App\Service\Audit\Dto\DecisionRecord $rec
     * @return void
     */
    public function log(DecisionRecord $rec): void
    {
        $this->repo->save($rec->toArray());
    }
}
