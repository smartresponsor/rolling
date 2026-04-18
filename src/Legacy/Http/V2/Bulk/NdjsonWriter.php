<?php

declare(strict_types=1);

<<<<<<< HEAD:src/Legacy/Http/V2/Bulk/NdjsonWriter.php
namespace App\Legacy\Http\V2\Bulk;
=======
namespace Http\Role\V2\Bulk;

>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa:Http/Role/V2/Bulk/NdjsonWriter.php
/**
 *
 */

/**
 *
 */
final class NdjsonWriter
{
    /**
     * @param $stream
     * @param array $obj
     */
    public function write($stream, array $obj): void
    {
        fwrite($stream, json_encode($obj, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n");
    }
}
