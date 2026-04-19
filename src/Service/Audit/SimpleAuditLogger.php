<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Service\Audit;

use App\InfrastructureInterface\Audit\AuditRepositoryInterface;
use App\Service\Audit\Dto\DecisionRecord;
use App\ServiceInterface\Audit\AuditLoggerInterface;

final class SimpleAuditLogger implements AuditLoggerInterface
{
    public function __construct(private readonly AuditRepositoryInterface $repo)
    {
    }

    public function log(DecisionRecord $rec): void
    {
        $this->repo->save($rec->toArray());
    }
}
