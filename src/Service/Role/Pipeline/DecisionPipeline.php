<?php

declare(strict_types=1);
/* Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp */

namespace Pipeline;

use App\Domain\Role\Model\RequestContext;

/**
 *
 */

/**
 *
 */
interface Stage
{
    /**
     * @param \App\Domain\Role\Model\RequestContext $ctx
     * @param \Pipeline\Trace $trace
     * @return \Pipeline\Decision|null
     */
    public function apply(RequestContext $ctx, Trace $trace): ?Decision;
}

/**
 *
 */

/**
 *
 */
final class DecisionPipeline
{
    /** @var array */
    private array $stages;

    /**
     * @param array $stages
     */
    public function __construct(array $stages)
    {
        $this->stages = $stages;
    }

    /**
     * @param \App\Domain\Role\Model\RequestContext $ctx
     * @return \Pipeline\Decision
     */
    public function evaluate(RequestContext $ctx): Decision
    {
        $trace = new Trace();
        foreach ($this->stages as $s) {
            $res = $s->apply($ctx, $trace);
            if ($res instanceof Decision) {
                return $res;
            }
        }
        $trace->add('pipeline', 'no-decision');
        return Decision::denied($trace, 'no-decision');
    }
}
