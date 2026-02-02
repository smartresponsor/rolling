<?php
declare(strict_types=1);
/* Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp */

namespace Pipeline\Stage;

use App\Domain\Role\Model\RequestContext;
use App\Service\Role\Pipeline\{Stage};
use Pipeline\Decision;
use Pipeline\Trace;

/**
 *
 */

/**
 *
 */
final class StrictDenyStage implements Stage
{
    /**
     * @param \Pipeline\Trace $trace
     * @return \Pipeline\Decision|null
     */
    public function apply(Trace $trace): ?Decision
    {
        $trace->add('policy', 'no-policy-deny');
        return Decision::denied($trace, 'no-policy');
    }
}
