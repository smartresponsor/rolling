<?php

declare(strict_types=1);

namespace Http\Role\V2\Bulk;

use Generator;

/**
 *
 */

/**
 *
 */
interface BulkReaderInterface
{
    /** @param resource $stream @return \Generator<int,array{subject:string,action:string,scope:array<string,mixed>,context:array<string,mixed>}> */
    public function items($stream): Generator;
}
