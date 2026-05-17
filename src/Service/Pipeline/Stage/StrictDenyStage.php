<?php

declare(strict_types=1);
/* Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp */

namespace App\Rolling\Service\Pipeline\Stage;

use App\Rolling\Service\Pipeline\RollingPipelineDecision;
use App\Rolling\Service\Pipeline\RollingPipelineRequestContext;
use App\Rolling\Service\Pipeline\RollingPipelineTrace;
use App\Rolling\ServiceInterface\Pipeline\StageInterface;

final class StrictDenyStage implements StageInterface
{
    public function apply(RollingPipelineRequestContext $ctx, RollingPipelineTrace $trace): ?RollingPipelineDecision
    {
        $trace->add('policy', 'no-policy-deny');

        return RollingPipelineDecision::denied($trace, 'no-policy');
    }
}
