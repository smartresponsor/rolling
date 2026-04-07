<?php
declare(strict_types=1);

namespace App\Service\Model;

/**
 *
 */

/**
 *
 */
final class Diff
{
    /**
     * @param array $from
     * @param array $to
     * @return array{breaking:bool, added:list<string>, removed:list<string>, changed:list<string>}
     */
    public static function compute(array $from, array $to): array
    {
        $fromR = array_keys($from['relations'] ?? []);
        $toR = array_keys($to['relations'] ?? []);
        $added = array_values(array_diff($toR, $fromR));
        $removed = array_values(array_diff($fromR, $toR));
        $changed = [];
        foreach (array_intersect($fromR, $toR) as $r) {
            $a = json_encode($from['relations'][$r]);
            $b = json_encode($to['relations'][$r]);
            if ($a !== $b) $changed[] = $r;
        }
        $breaking = count($removed) > 0;
        return compact('breaking', 'added', 'removed', 'changed');
    }
}
