<?php

declare(strict_types=1);

namespace App\Rolling\Service\Administration;

use App\Rolling\Entity\Acl\RollingRole;
use App\Rolling\Entity\Acl\RollingRoleHierarchy;
use App\Rolling\ServiceInterface\Administration\RollingRoleHierarchyMutationServiceInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;

/**
 * Doctrine-backed Rolling role-hierarchy mutation service.
 *
 * This service is intentionally narrow: Administering may submit reviewed
 * mutation payloads, but direct EasyAdmin editing stays disabled. Rolling owns
 * the validation and state transition semantics for hierarchy edges.
 */
final readonly class DoctrineRollingRoleHierarchyMutationService implements RollingRoleHierarchyMutationServiceInterface
{
    private const ACTION_ADD_EDGE = 'add_edge';
    private const ACTION_ENABLE_EDGE = 'enable_edge';
    private const ACTION_DISABLE_EDGE = 'disable_edge';

    public function __construct(private ManagerRegistry $registry)
    {
    }

    public function review(array $payload): array
    {
        $normalized = $this->normalize($payload);
        $messages = $this->validate($normalized, true);

        return [
            'status' => [] === $messages ? 'review_ready' : 'invalid',
            'messages' => [] === $messages ? ['Role hierarchy mutation is ready for owner review.'] : $messages,
            'review' => array_replace($normalized, $this->reviewMetadata($normalized)),
        ];
    }

    public function apply(array $payload, array $context = []): array
    {
        $normalized = $this->normalize($payload);
        $messages = $this->validate($normalized, true);
        if ([] !== $messages) {
            return [
                'status' => 'rejected',
                'messages' => $messages,
                'metadata' => ['review' => array_replace($normalized, $this->reviewMetadata($normalized))],
            ];
        }

        $manager = $this->manager();
        $edge = $manager->getRepository(RollingRoleHierarchy::class)->findOneBy([
            'parentRoleKey' => $normalized['parent_role_key'],
            'childRoleKey' => $normalized['child_role_key'],
        ]);

        $created = false;
        if (!$edge instanceof RollingRoleHierarchy) {
            if (self::ACTION_DISABLE_EDGE === $normalized['action']) {
                return [
                    'status' => 'not_found',
                    'messages' => ['Cannot disable a role hierarchy edge that does not exist.'],
                    'metadata' => ['review' => array_replace($normalized, $this->reviewMetadata($normalized))],
                ];
            }

            $edge = new RollingRoleHierarchy($normalized['parent_role_key'], $normalized['child_role_key']);
            $manager->persist($edge);
            $created = true;
        }

        if (self::ACTION_DISABLE_EDGE === $normalized['action']) {
            $edge->disable();
        } else {
            $edge->enable();
        }

        $manager->flush();

        return [
            'status' => 'applied',
            'messages' => [sprintf('Role hierarchy mutation "%s" was applied.', $normalized['action'])],
            'metadata' => [
                'review' => array_replace($normalized, $this->reviewMetadata($normalized)),
                'created_edge' => $created,
                'actor' => (string) ($context['actor'] ?? 'system'),
            ],
        ];
    }

    /** @return array{action:string,parent_role_key:string,child_role_key:string,justification:string} */
    private function normalize(array $payload): array
    {
        return [
            'action' => strtolower(trim((string) ($payload['hierarchy_mutation.action'] ?? $payload['action'] ?? 'add_edge'))),
            'parent_role_key' => trim((string) ($payload['hierarchy_mutation.parent_role_key'] ?? $payload['parent_role_key'] ?? '')),
            'child_role_key' => trim((string) ($payload['hierarchy_mutation.child_role_key'] ?? $payload['child_role_key'] ?? '')),
            'justification' => trim((string) ($payload['hierarchy_mutation.justification'] ?? $payload['justification'] ?? '')),
        ];
    }

    /**
     * @param array{action:string,parent_role_key:string,child_role_key:string,justification:string} $payload
     *
     * @return list<string>
     */
    private function validate(array $payload, bool $requireExistingRoles): array
    {
        $messages = [];
        if (!in_array($payload['action'], [self::ACTION_ADD_EDGE, self::ACTION_ENABLE_EDGE, self::ACTION_DISABLE_EDGE], true)) {
            $messages[] = 'Unsupported hierarchy mutation action.';
        }

        if ('' === $payload['parent_role_key'] || '' === $payload['child_role_key']) {
            $messages[] = 'Both parent and child role keys are required.';
        }

        if ('' !== $payload['parent_role_key'] && $payload['parent_role_key'] === $payload['child_role_key']) {
            $messages[] = 'A role cannot inherit from itself.';
        }

        if ('' === $payload['justification']) {
            $messages[] = 'A justification is required for reviewed hierarchy mutations.';
        }

        if ($requireExistingRoles && [] === $messages) {
            $manager = $this->manager();
            foreach (['parent_role_key', 'child_role_key'] as $field) {
                $role = $manager->getRepository(RollingRole::class)->findOneBy(['roleKey' => $payload[$field]]);
                if (!$role instanceof RollingRole) {
                    $messages[] = sprintf('Role "%s" does not exist. Synchronize or create roles before mutating hierarchy.', $payload[$field]);
                }
            }
        }

        if ([] === $messages && self::ACTION_DISABLE_EDGE !== $payload['action'] && $this->wouldCreateCycle($payload['parent_role_key'], $payload['child_role_key'])) {
            $messages[] = sprintf('Cannot enable hierarchy edge "%s > %s" because it would create a cycle.', $payload['parent_role_key'], $payload['child_role_key']);
        }

        return $messages;
    }

    /** @param array{action:string,parent_role_key:string,child_role_key:string,justification:string} $payload */
    private function reviewMetadata(array $payload): array
    {
        $manager = $this->manager();
        $edge = null;
        if ('' !== $payload['parent_role_key'] && '' !== $payload['child_role_key']) {
            $edge = $manager->getRepository(RollingRoleHierarchy::class)->findOneBy([
                'parentRoleKey' => $payload['parent_role_key'],
                'childRoleKey' => $payload['child_role_key'],
            ]);
        }

        return [
            'edge_exists' => $edge instanceof RollingRoleHierarchy,
            'edge_enabled' => $edge instanceof RollingRoleHierarchy ? $edge->isEnabled() : false,
            'cycle_check' => self::ACTION_DISABLE_EDGE === $payload['action'] ? 'not_applicable' : ($this->wouldCreateCycle($payload['parent_role_key'], $payload['child_role_key']) ? 'would_create_cycle' : 'clear'),
        ];
    }

    private function wouldCreateCycle(string $parentRoleKey, string $childRoleKey): bool
    {
        if ('' === $parentRoleKey || '' === $childRoleKey || $parentRoleKey === $childRoleKey) {
            return '' !== $parentRoleKey && $parentRoleKey === $childRoleKey;
        }

        $adjacency = $this->enabledHierarchyAdjacency($parentRoleKey, $childRoleKey);
        $stack = [$childRoleKey];
        $visited = [];

        while ([] !== $stack) {
            $current = array_pop($stack);
            if (!is_string($current) || isset($visited[$current])) {
                continue;
            }

            if ($current === $parentRoleKey) {
                return true;
            }

            $visited[$current] = true;
            foreach ($adjacency[$current] ?? [] as $next) {
                if (!isset($visited[$next])) {
                    $stack[] = $next;
                }
            }
        }

        return false;
    }

    /** @return array<string, list<string>> */
    private function enabledHierarchyAdjacency(string $proposedParentRoleKey, string $proposedChildRoleKey): array
    {
        $adjacency = [];
        $edges = $this->manager()->getRepository(RollingRoleHierarchy::class)->findBy(['enabled' => true]);
        foreach ($edges as $edge) {
            if (!$edge instanceof RollingRoleHierarchy) {
                continue;
            }

            $adjacency[$edge->parentRoleKey()][] = $edge->childRoleKey();
        }

        if (!in_array($proposedChildRoleKey, $adjacency[$proposedParentRoleKey] ?? [], true)) {
            $adjacency[$proposedParentRoleKey][] = $proposedChildRoleKey;
        }

        return $adjacency;
    }

    private function manager(): ObjectManager
    {
        $manager = $this->registry->getManagerForClass(RollingRoleHierarchy::class);
        if (null === $manager) {
            throw new \RuntimeException(sprintf('No Doctrine manager configured for %s.', RollingRoleHierarchy::class));
        }

        return $manager;
    }
}
