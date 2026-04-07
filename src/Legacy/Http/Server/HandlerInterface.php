<?php
declare(strict_types=1);

namespace App\Legacy\Http\Server;

use App\Legacy\Http\Response;

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
