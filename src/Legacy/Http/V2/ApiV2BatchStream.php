<?php
declare(strict_types=1);

namespace App\Legacy\Http\V2;

use App\Legacy\Policy\Batch\CheckBatchProcessor;

/**
 *
 */

/**
 *
 */
final class ApiV2BatchStream
{
    /**
     * @param \Policy\Role\Batch\CheckBatchProcessor $proc
     */
    public function __construct(private readonly CheckBatchProcessor $proc)
    {
    }

    /**
     * @param array{requests:list<array<string,mixed>>} $input
     * @param callable(string):void $emit Emits NDJSON chunk (string line already with \n)
     * @param int $chunkSize
     */
    public function stream(array $input, callable $emit, int $chunkSize = 128): void
    {
        $requests = (array)($input['requests'] ?? []);
        $gen = $this->proc->process($requests, ['chunkSize' => $chunkSize]);
        foreach ($gen as $row) {
            $emit(json_encode($row, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n");
        }
    }
}
