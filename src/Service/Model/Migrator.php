<?php

declare(strict_types=1);

namespace App\Rolling\Service\Model;

final class Migrator
{
    public function __construct(private readonly SchemaRegistry $registry)
    {
    }

    /** @return array{plan: array<string, mixed>, breaking: bool} */
    public function plan(array $from, array $to): array
    {
        $diff = Diff::compute($from, $to);
        $plan = [
            'add' => $diff['added'],
            'remove' => $diff['removed'],
            'change' => $diff['changed'],
        ];

        return ['plan' => $plan, 'breaking' => $diff['breaking']];
    }

    /** @return array{ok: bool, breaking: bool, activatedVersion: ?string} */
    public function apply(string $version, array $schema, bool $dryRun = false): array
    {
        $active = $this->registry->active();
        $from = null !== $active
            ? ($this->registry->get($active) ?? [])
            : ['namespace' => $schema['namespace'] ?? 'default', 'relations' => []];
        $plan = $this->plan($from, $schema);

        if ($dryRun) {
            return ['ok' => true, 'breaking' => $plan['breaking'], 'activatedVersion' => null];
        }

        $result = $this->registry->create($version, $schema);
        if (!$result['ok']) {
            return ['ok' => false, 'breaking' => $plan['breaking'], 'activatedVersion' => null];
        }

        $this->registry->activate($version);

        return ['ok' => true, 'breaking' => $plan['breaking'], 'activatedVersion' => $version];
    }
}
