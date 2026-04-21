<?php

declare(strict_types=1);
/* Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp */

namespace App\Rolling\Service\Pipeline\Stage;

use App\Rolling\Service\Pipeline\Decision;
use App\Rolling\Service\Pipeline\RequestContext;
use App\Rolling\Service\Pipeline\Trace;
use App\Rolling\ServiceInterface\Pipeline\StageInterface;

final class StrictDenyStage implements StageInterface
{
    public function apply(RequestContext $ctx, Trace $trace): ?Decision
    {
        $trace->add('policy', 'no-policy-deny');

        return Decision::denied($trace, 'no-policy');
    }
}
