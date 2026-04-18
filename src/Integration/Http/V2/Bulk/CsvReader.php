<?php

declare(strict_types=1);

namespace App\Integration\Http\V2\Bulk;

use Generator;

final class CsvReader implements BulkReaderInterface
{
    public function __construct(private readonly string $delimiter = ',') {}

    /**
     * @param resource $stream
     * @return Generator<int,array<string,mixed>>
     */
    public function items($stream): Generator
    {
        $header = null;
        while (($row = fgetcsv($stream, 0, $this->delimiter)) !== false) {
            if ($header === null) {
                $header = $row;
                continue;
            }
            $rec = array_combine($header, $row);
            if (!is_array($rec)) {
                continue;
            }
            $scope = ['type' => (string) ($rec['scope_type'] ?? 'global'), 'key' => (string) ($rec['scope_key'] ?? 'global')];
            if (!empty($rec['tenantId'])) {
                $scope['tenantId'] = (string) $rec['tenantId'];
            }
            if (!empty($rec['resourceId'])) {
                $scope['resourceId'] = (string) $rec['resourceId'];
            }
            $ctx = [];
            if (!empty($rec['context_json'])) {
                $parsed = json_decode((string) $rec['context_json'], true);
                if (is_array($parsed)) {
                    $ctx = $parsed;
                }
            }
            yield ['subject' => (string) ($rec['subject'] ?? ''), 'action' => (string) ($rec['action'] ?? ''), 'scope' => $scope, 'context' => $ctx];
        }
    }
}
