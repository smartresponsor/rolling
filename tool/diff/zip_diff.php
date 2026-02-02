#!/usr/bin/env php
<?php
declare(strict_types=1);

/**
 * Compare zip inventories (A vs B) → JSON summary.
 * usage: zip_diff.php <A.zip> <B.zip>
 */
$a = $argv[1] ?? null;
$b = $argv[2] ?? null;
if (!$a || !$b) {
    fwrite(STDERR, "usage: zip_diff.php <A.zip> <B.zip>\n");
    exit(2);
}
if (!is_file($a) || !is_file($b)) {
    fwrite(STDERR, "A or B zip not found\n");
    exit(2);
}

/**
 * @param string $p
 * @return array
 */
function listZip(string $p): array
{
    $z = new ZipArchive();
    if ($z->open($p) !== true) return [];
    $items = [];
    for ($i = 0; $i < $z->numFiles; $i++) {
        $stat = $z->statIndex($i);
        $items[] = $stat['name'];
    }
    $z->close();
    sort($items);
    return $items;
}

$A = listZip($a);
$B = listZip($b);
$setA = array_flip($A);
$setB = array_flip($B);

$added = array_values(array_diff($B, $A));
$removed = array_values(array_diff($A, $B));
$same = array_values(array_intersect($A, $B));

$rep = [
    'a' => $a, 'b' => $b,
    'counts' => ['added' => count($added), 'removed' => count($removed), 'same' => count($same)],
    'added' => $added, 'removed' => $removed,
];
@mkdir(__DIR__ . '/../../report', 0775, true);
$out = __DIR__ . '/../../report/zip_diff_g9.json';
file_put_contents($out, json_encode($rep, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "report/zip_diff_g9.json written\n";
