<?php
declare(strict_types=1);

namespace Policy\Role\Registry;

use App\Policy\Role\Obligation\Rules\{RedactFieldsRule, RuleSet, WatermarkRule};
use src\Entity\Role\{Scope};
use src\Entity\Role\PermissionKey;
use src\Entity\Role\SubjectId;

/**
 *
 */

/**
 *
 */
final class PolicyRegistry
{
    /**
     * @param \Policy\Role\Registry\SourceInterface $source
     * @param \Policy\Role\Registry\FlagEvaluator $flags
     */
    public function __construct(private readonly SourceInterface $source, private readonly FlagEvaluator $flags = new FlagEvaluator())
    {
    }

    /** @return array<string,mixed> */
    public function raw(): array
    {
        return $this->source->get();
    }

    /**
     * Построить RuleSet для конкретного действия с учётом включенных флагов и таргетинга.
     * @param \src\Entity\Role\SubjectId $subject
     * @param \src\Entity\Role\PermissionKey $action
     * @param \src\Entity\Role\Scope $scope
     * @param array $ctx
     * @return \App\Policy\Role\Obligation\Rules\RuleSet
     */
    public function ruleSetFor(SubjectId $subject, PermissionKey $action, Scope $scope, array $ctx = []): RuleSet
    {
        $cfg = $this->source->get();
        $useFlags = $this->flagsForAction($action->value(), $cfg);
        $rules = [];
        foreach ($useFlags as $flagName) {
            $flag = $cfg['flags'][$flagName] ?? null;
            if (!$flag || !$this->flags->isEnabled($flag, $subject, $action, $scope, $ctx)) {
                continue;
            }
            foreach ((array)($flag['rules'] ?? []) as $r) {
                $rule = $this->mkRule($r);
                if ($rule) {
                    $rules[] = $rule;
                }
            }
        }
        return new RuleSet($rules);
    }

    /** @return list<string> */
    private function flagsForAction(string $action, array $cfg): array
    {
        $out = [];
        foreach ((array)($cfg['routes'] ?? []) as $route) {
            foreach ((array)($route['actions'] ?? []) as $pat) {
                if ($this->matchAction($action, (string)$pat)) {
                    foreach ((array)($route['use'] ?? []) as $fn) {
                        $out[] = (string)$fn;
                    }
                }
            }
        }
        return array_values(array_unique($out));
    }

    /**
     * @param string $action
     * @param string $pattern
     * @return bool
     */
    private function matchAction(string $action, string $pattern): bool
    {
        if ($pattern === '*') return true;
        if (str_ends_with($pattern, '.*')) {
            $prefix = substr($pattern, 0, -2);
            return str_starts_with($action, $prefix . '.') || $action === $prefix;
        }
        return $action === $pattern;
    }

    /**
     * @param array $r
     * @return object|null
     */
    private function mkRule(array $r): ?object
    {
        $type = (string)($r['type'] ?? '');
        $params = (array)($r['params'] ?? []);
        return match ($type) {
            'redact_fields' => new RedactFieldsRule((array)($params['actions'] ?? ['*']), (array)($params['fields'] ?? [])),
            'watermark' => new WatermarkRule((string)($params['header'] ?? 'X-Policy'), (string)($params['value'] ?? '')),
            default => null,
        };
    }
}
