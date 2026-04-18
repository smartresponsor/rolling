<?php

declare(strict_types=1);

namespace App\Integration\Http\V2\Bulk;

use Generator;

interface BulkReaderInterface
{
    /**
     * @param resource $stream
     * @return Generator<int,array<string,mixed>>
     */
    public function items($stream): Generator;
}
