<?php
declare(strict_types=1);

namespace App\Legacy\Http\Server;

use App\Legacy\Http\Response;

interface MiddlewareInterface
{
    public function process(Request $request, HandlerInterface $handler): Response;
}
