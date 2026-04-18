<?php
<<<<<<< HEAD:src/Legacy/Http/Server/MiddlewareInterface.php
declare(strict_types=1);

namespace App\Legacy\Http\Server;

use App\Legacy\Http\Response;
=======

declare(strict_types=1);

namespace Http\Server;

use Http\Response;
>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa:Http/Server/MiddlewareInterface.php

interface MiddlewareInterface
{
    public function process(Request $request, HandlerInterface $handler): Response;
}
