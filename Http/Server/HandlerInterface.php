<?php
declare(strict_types=1);

namespace Http\Server;

use Http\Response;

/**
 *
 */

/**
 *
 */
interface HandlerInterface
{
    /**
     * @param \Http\Server\Request $request
     * @return \Http\Response
     */
    public function handle(Request $request): Response;
}
