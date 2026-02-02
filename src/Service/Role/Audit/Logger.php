<?php
declare(strict_types=1);

namespace Audit;

/**
 *
 */

/**
 *
 */
final class Logger
{
    /**
     * @param string $dir
     * @param \Audit\Redactor $redactor
     * @param bool $enabled
     */
    public function __construct(
        private readonly string   $dir = __DIR__ . '/../../../../var/log/role',
        private readonly Redactor $redactor = new Redactor(),
        private readonly bool     $enabled = true,
    )
    {
        if (!is_dir($this->dir)) @mkdir($this->dir, 0775, true);
    }

    /**
     * @param array $event @param array{mask?:list<string>,redact?:list<array{path:string,pattern:string}>} $obligations
     * @param array $obligations
     * @return array
     */
    public function write(array $event, array $obligations = []): array
    {
        if (!$this->enabled) return ['ok' => false, 'error' => 'disabled'];
        $ts = $event['ts'] ?? gmdate('c');
        $event['ts'] = $ts;
        // obligations -> redactor
        $mask = $obligations['mask'] ?? [];
        $redact = $obligations['redact'] ?? [];
        $red = new Redactor($mask, $redact);
        $san = $red->apply($event);
        $line = json_encode($san, JSON_UNESCAPED_SLASHES) . "\n";
        file_put_contents($this->dir . '/decision.jsonl', $line, FILE_APPEND);
        return ['ok' => true, 'meta' => ['audit' => 'logged', 'ts' => $ts, 'masked' => count($mask), 'redact' => count($redact)]];
    }

    /** @return list<array<string,mixed>> */
    public function tail(int $limit = 100): array
    {
        $path = $this->dir . '/decision.jsonl';
        if (!file_exists($path)) return [];
        $lines = @file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
        $slice = array_slice($lines, -$limit);
        return array_values(array_map(static function (string $l) {
            $d = json_decode($l, true);
            return is_array($d) ? $d : ['raw' => $l];
        }, $slice));
    }
}
