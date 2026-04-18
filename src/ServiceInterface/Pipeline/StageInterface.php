<?php
declare(strict_types=1);

namespace App\ServiceInterface\Pipeline;

use App\Service\Pipeline\RequestContext;
use App\Service\Pipeline\Decision;
use App\Service\Pipeline\Trace;

interface StageInterface
{
    public function apply(RequestContext $ctx, Trace $trace): ?Decision;
}
