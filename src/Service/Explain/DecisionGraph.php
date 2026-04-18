<?php

declare(strict_types=1);
/* Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp */

namespace App\Service\Explain;

<<<<<<< HEAD:src/Service/Explain/DecisionGraph.php
use App\Service\Pipeline\Trace;
=======
use Pipeline\Trace;
>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa:src/Service/Role/Explain/DecisionGraph.php

/**
 *
 */

/**
 *
 */
final class DecisionGraph
{
    /** @return array{nodes:array<int,array<string,mixed>>,edges:array<int,array{from:int,to:int,label:string}>} */
    public static function build(Trace $t): array
    {
        $steps = $t->all();
        $nodes = [];
        $edges = [];
        $prev = -1;
        foreach ($steps as $i => $s) {
            $nodes[] = ['id' => $i, 'stage' => $s['stage'], 'msg' => $s['msg'], 'ts' => $s['ts']];
            if ($prev >= 0) {
                $edges[] = ['from' => $prev, 'to' => $i, 'label' => 'next'];
            }
            $prev = $i;
        }
        return ['nodes' => $nodes, 'edges' => $edges];
    }
}
