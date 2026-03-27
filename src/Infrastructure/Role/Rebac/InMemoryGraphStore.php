<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Infra\Role\Rebac;

use App\InfrastructureInterface\Role\Rebac\GraphStoreInterface;
use src\ServiceInterface\Role\Rebac\NamespaceConstraintInterface;

/**
 * In-memory graph with tenant and namespace isolation.
 */
final class InMemoryGraphStore implements GraphStoreInterface
{
    /** @var array */
    private array $edges = []; // [tenant][namespace][] = edge

    /**
     * @param string $tenant
     * @param string $namespace
     * @param string $subject
     * @param string $relation
     * @param string $object
     * @return void
     */
    public function addEdge(string $tenant, string $namespace, string $subject, string $relation, string $object): void
    {
        $this->edges[$tenant][$namespace][] = [
            'subject' => $subject, 'relation' => $relation, 'object' => $object, 'namespace' => $namespace,
        ];
    }

    /**
     * @param string $tenant
     * @param string $namespace
     * @param string $subject
     * @param string $relation
     * @param string $object
     * @return void
     */
    public function removeEdge(string $tenant, string $namespace, string $subject, string $relation, string $object): void
    {
        $list = &$this->edges[$tenant][$namespace];
        if (!is_array($list)) {
            return;
        }
        $this->edges[$tenant][$namespace] = array_values(array_filter($list, function ($e) use ($subject, $relation, $object) {
            return !($e['subject'] === $subject && $e['relation'] === $relation && $e['object'] === $object);
        }));
    }

    /**
     * @param string $tenant
     * @param string $namespace
     * @param string $subject
     * @return array
     */
    public function edgesFrom(string $tenant, string $namespace, string $subject): array
    {
        $out = [];
        foreach ($this->edges[$tenant][$namespace] ?? [] as $e) {
            if ($e['subject'] === $subject) {
                $out[] = $e;
            }
        }
        return $out;
    }

    /**
     * @param string $tenant
     * @param string $startNamespace
     * @param string $subject
     * @param string $relation
     * @param string $object
     * @param \src\ServiceInterface\Role\Rebac\NamespaceConstraintInterface $constraints
     * @return bool
     */
    public function checkAccess(string $tenant, string $startNamespace, string $subject, string $relation, string $object, NamespaceConstraintInterface $constraints): bool
    {
        // Tenant boundary check is enforced at traversal; all edges are within single tenant map anyway.
        $visited = []; // key: namespace|node
        $q = [[$startNamespace, $subject]];

        while ($q) {
            [$ns, $node] = array_shift($q);
            $key = $ns . '|' . $node;
            if (isset($visited[$key])) {
                continue;
            }
            $visited[$key] = true;

            foreach ($this->edges[$tenant][$ns] ?? [] as $e) {
                if ($e['subject'] !== $node) {
                    continue;
                }
                $toNs = $e['namespace']; // same as $ns by storage, but keep pattern
                $nextNode = $e['object'];

                // Final hop check: relation must match and object equals
                if ($e['relation'] === $relation && $nextNode === $object) {
                    return true;
                }

                // Traverse if namespace transition allowed (including staying in same ns)
                if ($ns === $toNs || $constraints->canTraverse($ns, $toNs)) {
                    $q[] = [$toNs, $nextNode];
                }
            }

            // cross-namespace fanout: try all namespaces where transition is allowed (no edge copy)
            foreach ($this->edges[$tenant] ?? [] as $otherNs => $list) {
                if ($otherNs === $ns) {
                    continue;
                }
                if (!$constraints->canTraverse($ns, $otherNs)) {
                    continue;
                }
                foreach ($list as $e2) {
                    if ($e2['subject'] !== $node) {
                        continue;
                    }
                    $q[] = [$otherNs, $e2['object']];
                }
            }
        }
        return false;
    }
}
