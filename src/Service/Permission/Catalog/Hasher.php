<?php

declare(strict_types=1);

namespace App\Service\Permission\Catalog;

<<<<<<< HEAD:src/Service/Permission/Catalog/Hasher.php
use App\Service\Permission\Model\PermissionDef;

=======
>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa:src/Permission/Role/Catalog/Hasher.php
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
        return hash('sha256', (string) $json);
    }
}
