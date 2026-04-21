<?php

declare(strict_types=1);

namespace App\Rolling\Integration\Http\V2\Bulk;

interface BulkReaderInterface
{
    /**
     * @param resource $stream
     *
     * @return \Generator<int,array<string,mixed>>
     */
    public function items($stream): \Generator;
}
