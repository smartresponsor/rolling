<?php

declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Pipeline;

use App\Rolling\Service\Pipeline\Decision;
use App\Rolling\Service\Pipeline\RequestContext;
use App\Rolling\Service\Pipeline\Trace;

interface StageInterface
{
    public function apply(RequestContext $ctx, Trace $trace): ?Decision;
}
