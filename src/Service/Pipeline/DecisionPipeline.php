<?php
declare(strict_types=1);
/* Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp */

namespace App\Service\Pipeline;

use App\Legacy\Model\RequestContext;
use App\ServiceInterface\Pipeline\StageInterface;

final class DecisionPipeline
{
    /** @var array<int,StageInterface> */
    private array $stages;

    /** @param array<int,StageInterface> $stages */
    public function __construct(array $stages)
    {
        $this->stages = $stages;
    }

    public function evaluate(RequestContext $ctx): Decision
    {
        $trace = new Trace();

        foreach ($this->stages as $stage) {
            $result = $stage->apply($ctx, $trace);
            if ($result instanceof Decision) {
                return $result;
            }
        }

        $trace->add('pipeline', 'no-decision');

        return Decision::denied($trace, 'no-decision');
    }
}
