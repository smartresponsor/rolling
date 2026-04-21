<?php

declare(strict_types=1);

namespace App\Rolling\Service\Explain;

/**
 * Reads tuple change log from var/tuples.ndjson (as produced by D3 tools)
 * and materializes simple latest-state index.
 */
final class TupleReader
{
    /**
     * @param string $path
     */
    public function __construct(private readonly string $path = __DIR__.'/../../../../var/tuples.ndjson')
    {
    }

    /** @return array<int,array{tenant:string,subject:string,relation:string,resource:string,op:string,ts:string}> */
    public function readAll(): array
    {
        if (!file_exists($this->path)) {
            return [];
        }
        $out = [];
        $fh = fopen($this->path, 'r');
        if (false === $fh) {
            return [];
        }
        while (($line = fgets($fh)) !== false) {
            $line = trim($line);
            if ('' === $line) {
                continue;
            }
            $d = json_decode($line, true);
            if (!is_array($d)) {
                continue;
            }
            $d['tenant'] = (string) ($d['tenant'] ?? 't1');
            $d['subject'] = (string) ($d['subject'] ?? '');
            $d['relation'] = (string) ($d['relation'] ?? '');
            $d['resource'] = (string) ($d['resource'] ?? '');
            $d['op'] = (string) ($d['op'] ?? 'upsert');
            $d['ts'] = (string) ($d['ts'] ?? gmdate('c'));
            $out[] = $d;
        }
        fclose($fh);

        return $out;
    }

    /** Latest presence check by linear scan (dev-scale) */
    public function exists(string $tenant, string $subject, string $relation, string $resource): ?array
    {
        $last = null;
        foreach ($this->readAll() as $ev) {
            if ($ev['tenant'] === $tenant && $ev['subject'] === $subject && $ev['relation'] === $relation && $ev['resource'] === $resource) {
                $last = $ev; // last matching event serves as evidence
            }
        }

        return $last;
    }
}
