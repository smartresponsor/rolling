<?php

declare(strict_types=1);

<<<<<<< HEAD:src/Legacy/Http/Response.php
namespace App\Legacy\Http;
=======
namespace Http;

>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa:Http/Response.php
/**
 *
 */

/**
 *
 */
final class Response
{
    /**
     * @param int $status
     * @param array $headers
     * @param string $body
     */
    public function __construct(public int $status, public array $headers = [], public string $body = '') {}
}
