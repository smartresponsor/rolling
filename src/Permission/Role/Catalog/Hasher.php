<?php
declare(strict_types=1);

namespace App\Permission\Role\Catalog;

use App\Permission\Role\Model\PermissionDef;

/**
 *
 */

/**
 *
 */
final class Hasher
{
    /**
     * @param array $items
     * @return string
     */
    public function hash(array $items): string
    {
        usort($items, fn($a, $b) => strcmp($a->key, $b->key));
        $arr = array_map(fn($p) => $p->toArray(), $items);
        $json = json_encode($arr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);
        return hash('sha256', (string)$json);
    }
}
