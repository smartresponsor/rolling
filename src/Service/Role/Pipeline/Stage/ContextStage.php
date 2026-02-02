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
final class ContextStage implements Stage
{
    /**
     * @param \App\Domain\Role\Model\RequestContext $ctx
     * @param \Pipeline\Trace $trace
     * @return \Pipeline\Decision|null
     */
    public function apply(RequestContext $ctx, Trace $trace): ?Decision
    {
        $trace->add('context', 'normalized', ['tenant' => $ctx->tenant, 'subject' => $ctx->subject, 'action' => $ctx->action]);
        return null;
    }
}
