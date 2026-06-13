<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Observability\Metrics;

final class PrometheusExporter
{
    public function __construct(private readonly Registry $registry)
    {
    }

    public function render(): string
    {
        $lines = [];
        foreach ($this->registry->all() as $m) {
            $nameEntity = $this->sanitize($m->nameEntity());
            $help = $this->escape($m->help());
            $type = ($m instanceof Counter) ? 'counter' : 'histogram';
            $lines[] = "# HELP {$nameEntity} {$help}";
            $lines[] = "# TYPE {$nameEntity} {$type}";
            $d = $m->dump();
            if ($m instanceof Counter) {
                foreach ($d['series'] as $key => $val) {
                    $labels = $this->labels($d['names'], $key);
                    $lines[] = "{$nameEntity}{$labels} ".$this->fmt($val);
                }
            } else { // Histogram
                foreach ($d['data'] as $key => $row) {
                    $labels = $this->labels($d['names'], $key);
                    $acc = 0;
                    foreach ($m->buckets() as $b) {
                        $acc = $row['buckets'][(string) $b] ?? $acc;
                        $bstr = is_infinite($b) ? '+Inf' : (string) $b;
                        $bucketLabels = '' === $labels
                            ? '{le="'.$this->labelEscape($bstr).'"}'
                            : substr($labels, 0, -1).',le="'.$this->labelEscape($bstr).'"}';
                        $lines[] = "{$nameEntity}_bucket{$bucketLabels} ".$this->fmt($acc);
                    }
                    $lines[] = "{$nameEntity}_sum{$labels} ".$this->fmt($row['sum']);
                    $lines[] = "{$nameEntity}_count{$labels} ".(int) $row['count'];
                }
            }
        }

        return implode("\n", $lines)."\n";
    }

    private function sanitize(string $s): string
    {
        return preg_replace('/[^a-zA-Z0-9_:]/', '_', $s) ?? $s;
    }

    private function escape(string $s): string
    {
        return str_replace(['\\', "\n"], ['\\\\', '\\n'], $s);
    }

    private function fmt(float $v): string
    {
        return rtrim(rtrim(sprintf('%.6F', $v), '0'), '.');
    }

    private function labels(array $names, string $key): string
    {
        if (!$names) {
            return '';
        }
        $vals = explode("\x1f", $key);
        $pairs = [];
        foreach ($names as $i => $n) {
            $v = $vals[$i] ?? '';
            $pairs[] = $this->sanitize((string) $n).'="'.$this->labelEscape($v).'"';
        }

        return '{'.implode(',', $pairs).'}';
    }

    private function labelEscape(string $v): string
    {
        return str_replace(['\\', '"', "\n"], ['\\\\', '\\"', '\\n'], $v);
    }
}
