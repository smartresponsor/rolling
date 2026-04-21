<?php

declare(strict_types=1);
/* Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp */

namespace App\Rolling\Service\Pipeline\Stage;

use App\Rolling\Service\Pipeline\Decision;
use App\Rolling\Service\Pipeline\RequestContext;
use App\Rolling\Service\Pipeline\Trace;
use App\Rolling\ServiceInterface\Pipeline\StageInterface;

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
