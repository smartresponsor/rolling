<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\InfrastructureInterface\Rebac;

use App\Rolling\ServiceInterface\Rebac\NamespaceConstraintInterface;

/**
 * Minimal graph store API for ReBAC checks with namespaces and tenants.
 */
interface GraphStoreInterface
{
    /**
     * @param string $tenant
     * @param string $namespace
     * @param string $subject
     * @param string $relation
     * @param string $object
     *
     * @return void
     */
    public function addEdge(string $tenant, string $namespace, string $subject, string $relation, string $object): void;

    /**
     * @param string $tenant
     * @param string $namespace
     * @param string $subject
     * @param string $relation
     * @param string $object
     *
     * @return void
     */
    public function removeEdge(string $tenant, string $namespace, string $subject, string $relation, string $object): void;

    /** @return array<int,array{subject:string,relation:string,object:string,namespace:string}> */
    public function edgesFrom(string $tenant, string $namespace, string $subject): array;

    /**
     * Reachability check: is there a path from $subject → ... → $object
     * such that the last hop has $relation on the edge. Traversal across
     * namespaces is governed by NamespaceConstraintInterface.
     */
    public function checkAccess(
        string $tenant,
        string $startNamespace,
        string $subject,
        string $relation,
        string $object,
        NamespaceConstraintInterface $constraints,
    ): bool;
}
