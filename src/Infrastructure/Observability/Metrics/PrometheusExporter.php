<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Observability\Metrics;

final class PrometheusExporter
{
    /**
     * @param Registry $registry
     */
    public function __construct(private readonly Registry $registry)
    {
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $lines = [];
        foreach ($this->registry->all() as $m) {
            $name = $this->sanitize($m->name());
            $help = $this->escape($m->help());
            $type = ($m instanceof Counter) ? 'counter' : 'histogram';
            $lines[] = "# HELP {$name} {$help}";
            $lines[] = "# TYPE {$name} {$type}";
            $d = $m->dump();
            if ($m instanceof Counter) {
                foreach ($d['series'] as $key => $val) {
                    $labels = $this->labels($d['names'], $key);
                    $lines[] = "{$name}{$labels} ".$this->fmt($val);
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
                        $lines[] = "{$name}_bucket{$bucketLabels} ".$this->fmt($acc);
                    }
                    $lines[] = "{$name}_sum{$labels} ".$this->fmt($row['sum']);
                    $lines[] = "{$name}_count{$labels} ".(int) $row['count'];
                }
            }
        }

        return implode("\n", $lines)."\n";
    }

    /**
     * @param string $s
     *
     * @return string
     */
    private function sanitize(string $s): string
    {
        return preg_replace('/[^a-zA-Z0-9_:]/', '_', $s) ?? $s;
    }

    /**
     * @param string $s
     *
     * @return string
     */
    private function escape(string $s): string
    {
        return str_replace(['\\', "\n"], ['\\\\', '\\n'], $s);
    }

    /**
     * @param float $v
     *
     * @return string
     */
    private function fmt(float $v): string
    {
        return rtrim(rtrim(sprintf('%.6F', $v), '0'), '.');
    }

    /**
     * @param array  $names
     * @param string $key
     *
     * @return string
     */
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

    /**
     * @param string $v
     *
     * @return string
     */
    private function labelEscape(string $v): string
    {
        return str_replace(['\\', '"', "\n"], ['\\\\', '\\"', '\\n'], $v);
    }
}
