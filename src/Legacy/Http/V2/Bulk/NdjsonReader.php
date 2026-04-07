<?php
declare(strict_types=1);

namespace App\Legacy\Http\V2\Bulk;

use Generator;

/**
 *
 */

/**
 *
 */
final class NdjsonReader implements BulkReaderInterface
{
    /**
     * @param $stream
     * @return \Generator
     */
    /**
     * @param $stream
     * @return \Generator
     */
    public function items($stream): Generator
    {
        while (!feof($stream)) {
            $line = fgets($stream);
            if ($line === false) break;
            $line = trim($line);
            if ($line === '') continue;
            $row = json_decode($line, true);
            if (!is_array($row)) continue;
            yield ['subject' => (string)($row['subject'] ?? ''), 'action' => (string)($row['action'] ?? ''), 'scope' => (array)($row['scope'] ?? []), 'context' => (array)($row['context'] ?? []),];
        }
    }
}
