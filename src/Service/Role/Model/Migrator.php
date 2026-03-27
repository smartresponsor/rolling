<?php

declare(strict_types=1);

namespace Model;

/**
 *
 */

/**
 *
 */
final class Migrator
{
    /**
     * @param \Model\SchemaRegistry $registry
     */
    public function __construct(private readonly SchemaRegistry $registry) {}

    /** @return array{plan:array, breaking:bool} */
    public function plan(array $from, array $to): array
    {
        $d = Diff::compute($from, $to);
        $plan = [
            'add' => $d['added'],
            'remove' => $d['removed'],
            'change' => $d['changed'],
        ];
        return ['plan' => $plan, 'breaking' => $d['breaking']];
    }

    /** @return array{ok:bool, breaking:bool, activated:?string} */
    public function apply(string $version, array $schema, bool $dryRun = false): array
    {
        $active = $this->registry->active();
        $from = $active ? ($this->registry->get($active) ?? []) : ['namespace' => $schema['namespace'] ?? 'default', 'relations' => []];
        $p = $this->plan($from, $schema);
        if ($dryRun) {
            return ['ok' => true, 'breaking' => $p['breaking'], 'activated' => null];
        }
        $res = $this->registry->create($version, $schema);
        if (!$res['ok']) {
            return ['ok' => false, 'breaking' => $p['breaking'], 'activated' => null];
        }
        $this->registry->activate($version);
        return ['ok' => true, 'breaking' => $p['breaking'], 'activated' => $version];
    }
}
