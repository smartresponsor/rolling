<?php
declare(strict_types=1);

namespace App\Service\Admin;

use App\Infrastructure\Rebac\PdoTupleStore;
use ReflectionClass;

/**
 *
 */

/**
 *
 */
final class RebacStatsService
{
    /**
     * @param object $store
     */
    public function __construct(private readonly object $store)
    {
    }

    /** @return array{ns:string,tuples:int} */
    public function stats(string $ns): array
    {
        // Fast path: PDO
        if ($this->store instanceof PdoTupleStore) {
            $ref = new ReflectionClass($this->store);
            $prop = $ref->getProperty('pdo');
            $prop->setAccessible(true);
            /** @var \PDO $pdo */
            $pdo = $prop->getValue($this->store);
            $st = $pdo->prepare("SELECT COUNT(*) FROM role_tuple WHERE ns=?");
            $st->execute([$ns]);
            $cnt = (int)$st->fetchColumn();
            return ['ns' => $ns, 'tuples' => $cnt];
        }
        // Fallback: iterate (may be slow), requires readBySubject readByObject not efficient; skip deep stats
        // Here we can't iterate all easily; return unknown marker
        return ['ns' => $ns, 'tuples' => -1];
    }
}
