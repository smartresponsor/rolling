<?php

declare(strict_types=1);

namespace Http\Server;

use Http\Response;

interface MiddlewareInterface
{
    public function process(Request $request, HandlerInterface $handler): Response;
}
