<?php

declare(strict_types=1);

namespace Explain;

/**
 *
 */

/**
 *
 */
final class Renderer
{
    /**
     * @param array $nodes @param list<array{from:string,to:string,label:string}> $edges
     * @param array $edges
     * @return string
     */
    public static function toDot(array $nodes, array $edges): string
    {
        $lines = ['digraph G {'];
        foreach ($nodes as $n) {
            $shape = match ($n['type']) {
                'tenant' => 'folder',
                'subject' => 'ellipse',
                'relation' => 'diamond',
                default => 'box',
            };
            $lines[] = sprintf('  "%s" [label="%s", shape=%s];', addslashes($n['id']), addslashes($n['label']), $shape);
        }
        foreach ($edges as $e) {
            $lines[] = sprintf('  "%s" -> "%s" [label="%s"];', addslashes($e['from']), addslashes($e['to']), addslashes($e['label']));
        }
        $lines[] = '}';
        return implode("\n", $lines);
    }
}
