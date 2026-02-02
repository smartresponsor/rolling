#!/usr/bin/env php
<?php
declare(strict_types=1);

/**
 * Simple line-diff (A vs B) → JSON summary.
 * usage: file_diff.php <A> <B>
 */
$a = $argv[1] ?? null;
$b = $argv[2] ?? null;
if (!$a || !$b) {
    fwrite(STDERR, "usage: file_diff.php <A> <B>\n");
    exit(2);
}
if (!is_file($a) || !is_file($b)) {
    fwrite(STDERR, "A or B not found\n");
    exit(2);
}

$A = file($a, FILE_IGNORE_NEW_LINES);
$B = file($b, FILE_IGNORE_NEW_LINES);
$setA = array_flip($A);
$setB = array_flip($B);

$added = [];
$removed = [];
foreach ($B as $line) if (!isset($setA[$line])) $added[] = $line;
foreach ($A as $line) if (!isset($setB[$line])) $removed[] = $line;

$rep = [
    'a' => $a, 'b' => $b,
    'added' => $added, 'removed' => $removed,
    'counts' => ['added' => count($added), 'removed' => count($removed)],
];

@mkdir(__DIR__ . '/../../report', 0775, true);
$out = __DIR__ . '/../../report/file_diff_g9.json';
file_put_contents($out, json_encode($rep, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "report/file_diff_g9.json written\n";
