<?php

declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Pipeline;

use App\Rolling\Service\Pipeline\RollingPipelineDecision;
use App\Rolling\Service\Pipeline\RollingPipelineRequestContext;
use App\Rolling\Service\Pipeline\RollingPipelineTrace;

interface StageInterface
{
    public function apply(RollingPipelineRequestContext $ctx, RollingPipelineTrace $trace): ?RollingPipelineDecision;
}
