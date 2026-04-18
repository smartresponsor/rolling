<?php

declare(strict_types=1);

namespace App\Integration\Http\V2\Bulk;

final class NdjsonWriter
{
    /**
     * @param resource $stream
     * @param array<string,mixed> $obj
     */
    public function write($stream, array $obj): void
    {
        fwrite($stream, json_encode($obj, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n");
    }
}
