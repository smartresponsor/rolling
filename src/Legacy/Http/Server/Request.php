<?php

declare(strict_types=1);

<<<<<<< HEAD:src/Legacy/Http/Server/Request.php
namespace App\Legacy\Http\Server;
=======
namespace Http\Server;

>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa:Http/Server/Request.php
/**
 *
 */

/**
 *
 */
interface Request
{
    /**
     * @return string
     */
    public function method(): string;

    /**
     * @return string
     */
    public function path(): string;

    /**
     * @param string $name
     * @return string|null
     */
    public function header(string $name): ?string;

    /**
     * @return string
     */
    public function body(): string;
}
