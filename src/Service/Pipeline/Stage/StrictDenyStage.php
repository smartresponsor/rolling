<?php
declare(strict_types=1);
/* Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp */

namespace App\Service\Pipeline\Stage;

use App\Legacy\Model\RequestContext;
use App\Service\Pipeline\Decision;
use App\Service\Pipeline\Trace;
use App\ServiceInterface\Pipeline\StageInterface;

final class StrictDenyStage implements StageInterface
{
    public function apply(RequestContext $ctx, Trace $trace): ?Decision
    {
        $trace->add('policy', 'no-policy-deny');
        return Decision::denied($trace, 'no-policy');
    }
}
