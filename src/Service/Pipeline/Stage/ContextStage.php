<?php
declare(strict_types=1);
/* Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp */

namespace App\Service\Pipeline\Stage;

use App\Service\Pipeline\RequestContext;
use App\Service\Pipeline\Decision;
use App\Service\Pipeline\Trace;
use App\ServiceInterface\Pipeline\StageInterface;

final class ContextStage implements StageInterface
{
    public function apply(RequestContext $ctx, Trace $trace): ?Decision
    {
        $trace->add('context', 'normalized', [
            'tenant' => $ctx->tenant,
            'subject' => $ctx->subject,
            'action' => $ctx->action,
        ]);

        return null;
    }
}
