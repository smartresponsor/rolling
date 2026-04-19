<?php

declare(strict_types=1);

namespace App\Service\Explain;

final class Planner
{
    public function __construct(private readonly TupleReader $reader)
    {
    }

    /**
     * @return array{
     *     allowed: bool,
     *     token: string,
     *     nodes: list<array{id: string, label: string, type: string}>,
     *     edges: list<array{from: string, to: string, label: string}>,
     *     evidence: array<string, mixed>|null
     * }
     */
    public function plan(string $tenant, string $subject, string $relation, string $resource): array
    {
        $token = (string) (@filesize(__DIR__.'/../../../../var/tuples.ndjson') ?: 0);
        $evidence = $this->reader->exists($tenant, $subject, $relation, $resource);
        $allowed = null !== $evidence;
        $nodes = [
            ['id' => "tenant:$tenant", 'label' => "tenant:$tenant", 'type' => 'tenant'],
            ['id' => "subject:$subject", 'label' => "subject:$subject", 'type' => 'subject'],
            ['id' => "relation:$relation", 'label' => "relation:$relation", 'type' => 'relation'],
            ['id' => "resource:$resource", 'label' => "resource:$resource", 'type' => 'resource'],
        ];
        $edges = [
            ['from' => "tenant:$tenant", 'to' => "subject:$subject", 'label' => 'scoped'],
            ['from' => "subject:$subject", 'to' => "relation:$relation", 'label' => 'wants'],
            ['from' => "relation:$relation", 'to' => "resource:$resource", 'label' => $allowed ? 'proven' : 'missing'],
        ];

        return compact('allowed', 'token', 'nodes', 'edges', 'evidence');
    }
}
