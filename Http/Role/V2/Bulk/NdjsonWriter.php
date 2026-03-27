<?php

declare(strict_types=1);

namespace Http\Role\V2\Bulk;

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
