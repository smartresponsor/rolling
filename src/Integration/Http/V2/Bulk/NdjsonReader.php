<?php

declare(strict_types=1);

namespace App\Rolling\Integration\Http\V2\Bulk;

final class NdjsonReader implements BulkReaderInterface
{
    /**
     * @param resource $stream
     *
     * @return \Generator<int,array<string,mixed>>
     */
    public function items($stream): \Generator
    {
        while (!feof($stream)) {
            $line = fgets($stream);
            if (false === $line) {
                break;
            }
            $line = trim($line);
            if ('' === $line) {
                continue;
            }
            $row = json_decode($line, true);
            if (!is_array($row)) {
                continue;
            }
            yield [
                'subject' => (string) ($row['subject'] ?? ''),
                'action' => (string) ($row['action'] ?? ''),
                'scope' => (array) ($row['scope'] ?? []),
                'context' => (array) ($row['context'] ?? []),
            ];
        }
    }
}
