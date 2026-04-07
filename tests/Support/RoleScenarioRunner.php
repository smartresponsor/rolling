<?php

declare(strict_types=1);

namespace App\Tests\Support;

use App\Legacy\Model\Rebac\Tuple;
use App\Service\Rebac\Checker;
use App\Service\Rebac\Writer;
use App\Infrastructure\Rebac\InMemoryTupleStore;
use InvalidArgumentException;

final class RoleScenarioRunner
{
    public static function runBaseline(array $fixture): array
    {
        return self::execute($fixture, null, 'baseline');
    }

    public static function preview(array $fixture, string $scenario): array
    {
        return self::execute($fixture, $scenario, 'preview');
    }

    public static function runScenario(array $fixture, string $scenario): array
    {
        return self::execute($fixture, $scenario, 'run');
    }

    public static function explain(array $fixture, string $subject, string $object, string $relation, ?string $scenario = null): array
    {
        $mode = $scenario === null ? 'explain' : 'explain-scenario';
        [$store, $writer, $checker] = self::bootEngine($fixture);
        $operations = ['writes' => [], 'deletes' => []];

        if ($scenario !== null) {
            $spec = self::scenarioSpec($fixture, $scenario);
            $operations = self::applyOperations($writer, (string) ($fixture['ns'] ?? 'default'), $spec, false);
        }

        $result = self::evaluateSingleCheck($checker, self::checkRow(
            (string) ($fixture['ns'] ?? 'default'),
            [
                'subject' => $subject,
                'object' => $object,
                'relation' => $relation,
                'allow' => true,
            ],
        ));

        return [
            'fixture' => $fixture['name'] ?? 'unknown',
            'mode' => $mode,
            'scenario' => $scenario,
            'summary' => [
                'writes' => count($operations['writes']),
                'deletes' => count($operations['deletes']),
                'allow' => (bool) $result['actual'],
            ],
            'ok' => true,
            'operations' => $operations,
            'explanation' => $result,
        ];
    }

    public static function audit(array $fixture): array
    {
        $baseline = self::runBaseline($fixture);
        $scenarios = [];
        foreach (self::scenarioNames($fixture) as $scenarioName) {
            $scenarios[$scenarioName] = self::runScenario($fixture, $scenarioName);
        }

        return [
            'fixture' => $fixture['name'] ?? 'unknown',
            'mode' => 'audit',
            'ok' => $baseline['ok'] && array_reduce($scenarios, static fn(bool $carry, array $row): bool => $carry && (bool) ($row['ok'] ?? false), true),
            'summary' => [
                'scenario_count' => count($scenarios),
                'baseline_before_checks' => (int) ($baseline['summary']['before_checks'] ?? 0),
                'baseline_ok' => (bool) ($baseline['ok'] ?? false),
            ],
            'baseline' => $baseline,
            'scenarios' => $scenarios,
        ];
    }

    public static function scenarioNames(array $fixture): array
    {
        return array_keys($fixture['scenarios'] ?? []);
    }

    public static function run(array $fixture): array
    {
        return self::runBaseline($fixture);
    }

    private static function execute(array $fixture, ?string $scenario, string $mode): array
    {
        if (($fixture['engine'] ?? null) !== 'rebac-minimal') {
            throw new InvalidArgumentException('Unsupported fixture engine.');
        }

        [$store, $writer, $checker] = self::bootEngine($fixture);

        $before = self::evaluateChecks($checker, (string) ($fixture['ns'] ?? 'default'), $fixture['checks'] ?? []);
        $operations = ['writes' => [], 'deletes' => []];
        $after = null;

        if ($scenario !== null) {
            $spec = self::scenarioSpec($fixture, $scenario);
            $operations = self::applyOperations($writer, (string) ($fixture['ns'] ?? 'default'), $spec, false);
            $after = self::evaluateChecks($checker, (string) ($fixture['ns'] ?? 'default'), $spec['checks'] ?? []);
        }

        return [
            'fixture' => $fixture['name'] ?? 'unknown',
            'engine' => 'rebac-minimal',
            'mode' => $mode,
            'scenario' => $scenario,
            'summary' => self::buildSummary($before, $after, $operations),
            'ok' => ($before['ok'] ?? true) && ($after['ok'] ?? true),
            'before' => $before,
            'operations' => $operations,
            'after' => $after,
        ];
    }

    /**
     * @return array{0:InMemoryTupleStore,1:Writer,2:Checker}
     */
    private static function bootEngine(array $fixture): array
    {
        $store = new InMemoryTupleStore();
        $writer = new Writer($store);
        $checker = new Checker($store);

        $seed = [];
        foreach (($fixture['seed'] ?? $fixture['tuples'] ?? []) as $row) {
            $seed[] = self::tupleFromRow((string) ($row['ns'] ?? $fixture['ns'] ?? 'default'), $row);
        }

        if ($seed !== []) {
            $writer->write((string) ($fixture['ns'] ?? 'default'), $seed);
        }

        return [$store, $writer, $checker];
    }

    private static function scenarioSpec(array $fixture, string $scenario): array
    {
        $spec = $fixture['scenarios'][$scenario] ?? null;
        if (!is_array($spec)) {
            throw new InvalidArgumentException(sprintf('Unknown scenario "%s".', $scenario));
        }

        return $spec;
    }

    /**
     * @return array{writes:list<array<string,mixed>>,deletes:list<array<string,mixed>>}
     */
    private static function applyOperations(Writer $writer, string $ns, array $spec, bool $preview): array
    {
        $writes = [];
        foreach (($spec['writes'] ?? []) as $row) {
            $writes[] = $row;
        }
        if ($writes !== [] && !$preview) {
            foreach ($writes as $row) {
                $writer->write((string) ($row['ns'] ?? $ns), [self::tupleFromRow((string) ($row['ns'] ?? $ns), $row)]);
            }
        }

        $deletes = [];
        foreach (($spec['deletes'] ?? []) as $row) {
            $deletes[] = $row;
        }
        if (!$preview) {
            foreach ($deletes as $row) {
                $writer->delete((string) ($row['ns'] ?? $ns), self::tupleFromRow((string) ($row['ns'] ?? $ns), $row));
            }
        }

        return [
            'writes' => $writes,
            'deletes' => $deletes,
        ];
    }

    /**
     * @param list<array<string,mixed>> $rows
     * @return array{ok:bool,checks:list<array<string,mixed>>}
     */
    private static function evaluateChecks(Checker $checker, string $defaultNs, array $rows): array
    {
        $checks = [];
        $ok = true;

        foreach ($rows as $row) {
            $result = self::evaluateSingleCheck($checker, self::checkRow($defaultNs, $row));
            $checks[] = $result;
            if ((bool) $result['actual'] !== (bool) $result['expected']) {
                $ok = false;
            }
        }

        return [
            'ok' => $ok,
            'checks' => $checks,
        ];
    }

    /**
     * @param array<string,mixed> $row
     * @return array<string,mixed>
     */
    private static function checkRow(string $defaultNs, array $row): array
    {
        return [
            'ns' => (string) ($row['ns'] ?? $defaultNs),
            'subject' => (string) $row['subject'],
            'object' => (string) $row['object'],
            'relation' => (string) $row['relation'],
            'allow' => (bool) ($row['allow'] ?? false),
            'label' => isset($row['label']) ? (string) $row['label'] : null,
        ];
    }

    /**
     * @param array<string,mixed> $row
     * @return array<string,mixed>
     */
    private static function evaluateSingleCheck(Checker $checker, array $row): array
    {
        $actual = $checker->check((string) $row['ns'], (string) $row['subject'], (string) $row['object'], (string) $row['relation']);
        $expected = (bool) $row['allow'];

        return [
            'ns' => $row['ns'],
            'label' => $row['label'],
            'subject' => $row['subject'],
            'object' => $row['object'],
            'relation' => $row['relation'],
            'expected' => $expected,
            'actual' => (bool) $actual['allow'],
            'result' => $actual,
        ];
    }

    /**
     * @param array{ok:bool,checks:list<array<string,mixed>>} $before
     * @param array{ok:bool,checks:list<array<string,mixed>>}|null $after
     * @param array{writes:list<array<string,mixed>>,deletes:list<array<string,mixed>>} $operations
     * @return array<string,int|bool>
     */
    private static function buildSummary(array $before, ?array $after, array $operations): array
    {
        return [
            'before_checks' => count($before['checks'] ?? []),
            'after_checks' => count($after['checks'] ?? []),
            'writes' => count($operations['writes'] ?? []),
            'deletes' => count($operations['deletes'] ?? []),
            'before_ok' => (bool) ($before['ok'] ?? false),
            'after_ok' => (bool) ($after['ok'] ?? false),
        ];
    }

    /**
     * @param array<string,mixed> $row
     */
    private static function tupleFromRow(string $ns, array $row): Tuple
    {
        return new Tuple(
            $ns,
            (string) $row['objType'],
            (string) $row['objId'],
            (string) $row['relation'],
            (string) $row['subjType'],
            (string) $row['subjId'],
            isset($row['subjRel']) ? (string) $row['subjRel'] : null,
        );
    }
}
