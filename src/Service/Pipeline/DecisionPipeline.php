<?php

declare(strict_types=1);
/* Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp */

namespace App\Rolling\Service\Pipeline;

use App\Rolling\ServiceInterface\Pipeline\StageInterface;

final class DecisionPipeline
{
    /** @var array<int,StageInterface> */
    private array $stages;

    /** @param array<int,StageInterface> $stages */
    public function __construct(array $stages)
    {
        $this->stages = $stages;
    }

    public function evaluate(RollingPipelineRequestContext $ctx): RollingPipelineDecision
    {
        $trace = new RollingPipelineTrace();

        foreach ($this->stages as $stage) {
            $result = $stage->apply($ctx, $trace);
            if ($result instanceof RollingPipelineDecision) {
                return $result;
            }
        }

        $trace->add('pipeline', 'no-decision');

        return RollingPipelineDecision::denied($trace, 'no-decision');
    }
}
