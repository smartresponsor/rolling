<?php

declare(strict_types=1);
/* Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp */

namespace App\Rolling\Service\Pipeline\Stage;

use App\Rolling\Service\Pipeline\RollingPipelineDecision;
use App\Rolling\Service\Pipeline\RollingPipelineRequestContext;
use App\Rolling\Service\Pipeline\RollingPipelineTrace;
use App\Rolling\ServiceInterface\Pipeline\StageInterface;

final class ContextStage implements StageInterface
{
    public function apply(RollingPipelineRequestContext $ctx, RollingPipelineTrace $trace): ?RollingPipelineDecision
    {
        $trace->add('context', 'normalized', [
            'tenant' => $ctx->tenant,
            'subject' => $ctx->subject,
            'action' => $ctx->action,
        ]);

        return null;
    }
}
