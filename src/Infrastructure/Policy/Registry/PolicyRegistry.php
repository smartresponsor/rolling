<?php

declare(strict_types=1);

namespace App\Infrastructure\Policy\Registry;

use App\Entity\Role\PermissionKey;
use App\Entity\Role\Scope;
use App\Entity\Role\SubjectId;
use App\Policy\Obligation\Rules\RedactFieldsRule;
use App\Policy\Obligation\Rules\RuleSet;
use App\Policy\Obligation\Rules\WatermarkRule;

final class PolicyRegistry
{
    public function __construct(private readonly SourceInterface $source, private readonly FlagEvaluator $flags = new FlagEvaluator()) {}

    /** @return array<string,mixed> */
    public function raw(): array
    {
        return $this->source->get();
    }

    /**
     * @param array<string,mixed> $ctx
     */
    public function ruleSetFor(SubjectId $subject, PermissionKey $action, Scope $scope, array $ctx = []): RuleSet
    {
        $cfg = $this->source->get();
        $useFlags = $this->flagsForAction($action->value(), $cfg);
        $rules = [];
        foreach ($useFlags as $flagName) {
            $flag = $cfg['flags'][$flagName] ?? null;
            if (!is_array($flag) || !$this->flags->isEnabled($flag, $subject, $action, $scope, $ctx)) {
                continue;
            }
            foreach ((array) ($flag['rules'] ?? []) as $r) {
                if (is_array($r)) {
                    $rule = $this->mkRule($r);
                    if ($rule !== null) {
                        $rules[] = $rule;
                    }
                }
            }
        }
        return new RuleSet($rules);
    }

    /**
     * @param array<string,mixed> $cfg
     * @return list<string>
     */
    private function flagsForAction(string $action, array $cfg): array
    {
        $out = [];
        foreach ((array) ($cfg['routes'] ?? []) as $route) {
            if (!is_array($route)) {
                continue;
            }
            foreach ((array) ($route['actions'] ?? []) as $pat) {
                if ($this->matchAction($action, (string) $pat)) {
                    foreach ((array) ($route['use'] ?? []) as $fn) {
                        $out[] = (string) $fn;
                    }
                }
            }
        }
        return array_values(array_unique($out));
    }

    private function matchAction(string $action, string $pattern): bool
    {
        if ($pattern === '*') {
            return true;
        }
        if (str_ends_with($pattern, '.*')) {
            $prefix = substr($pattern, 0, -2);
            return str_starts_with($action, $prefix . '.') || $action === $prefix;
        }
        return $action === $pattern;
    }

    /**
     * @param array<string,mixed> $r
     */
    private function mkRule(array $r): ?object
    {
        $type = (string) ($r['type'] ?? '');
        $params = (array) ($r['params'] ?? []);
        return match ($type) {
            'redact_fields' => new RedactFieldsRule((array) ($params['actions'] ?? ['*']), (array) ($params['fields'] ?? [])),
            'watermark' => new WatermarkRule((string) ($params['header'] ?? 'X-Policy'), (string) ($params['value'] ?? '')),
            default => null,
        };
    }
}
