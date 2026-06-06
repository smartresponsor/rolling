<?php

declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Administration;

/**
 * Rolling-owned review/apply boundary for role hierarchy mutations.
 *
 * Administering may render forms and submit payloads, but hierarchy mutation
 * semantics, validation, and Doctrine writes belong to Rolling.
 */
interface RollingRoleHierarchyMutationServiceInterface
{
    /**
     * @param array<string, mixed> $payload
     *
     * @return array{status:string, messages:list<string>, review:array<string, mixed>}
     */
    public function review(array $payload): array;

    /**
     * @param array<string, mixed> $payload
     * @param array<string, mixed> $context
     *
     * @return array{status:string, messages:list<string>, metadata:array<string, mixed>}
     */
    public function apply(array $payload, array $context = []): array;
}
