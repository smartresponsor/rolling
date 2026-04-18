<?php

declare(strict_types=1);
/* Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp */

namespace App\Service\Pipeline\Stage;

use App\Service\Pipeline\RequestContext;
use App\Service\Pipeline\Decision;
use App\Service\Pipeline\Trace;
use App\ServiceInterface\Pipeline\StageInterface;
use Throwable;

final class PolicyStage implements StageInterface
{
    /** @var array<string,string> */
    private array $pol;

    /** @param array<string,string> $pol */
    public function __construct(array $pol)
    {
        $this->pol = $pol;
    }

    public function apply(RequestContext $ctx, Trace $trace): ?Decision
    {
        $expr = $this->pol[$ctx->tenant] ?? '';
        if ($expr === '') {
            $trace->add('policy', 'empty');
            return null;
        }

        $map = [
            'action' => $ctx->action,
            'subject.role' => $ctx->attrs['role'] ?? ($ctx->resource['role'] ?? ''),
            'resource.type' => $ctx->resource['type'] ?? '',
        ];

        foreach ($ctx->attrs as $key => $value) {
            $map['attrs.' . $key] = is_scalar($value) ? (string) $value : json_encode($value);
        }

        $expr2 = str_replace([' and ', ' or '], [' && ', ' || '], $expr);
        $expr2 = preg_replace('/\b([A-Za-z0-9_\-\.]+)\s+in\s+\[/', 'in_array(\1, [', $expr2) ?? $expr2;
        $expr2 = preg_replace_callback('/\b(subject\.role|action|resource\.type|attrs\.[A-Za-z0-9_\-]+)\b/', static function (array $m) use ($map): string {
            $value = $map[$m[0]] ?? null;
            if (is_string($value)) {
                return "'" . str_replace("'", "\\'", $value) . "'";
            }
            if (is_bool($value)) {
                return $value ? 'true' : 'false';
            }
            if (is_numeric($value)) {
                return (string) $value;
            }
            return 'null';
        }, $expr2) ?? $expr2;

        if (preg_match('/[^A-Za-z0-9_\-\(\)\[\]\,\'\"\s\&\|\!\.]/', $expr2) === 1) {
            $trace->add('policy', 'invalid');
            return null;
        }

        $code = "<?php\nreturn (bool)($expr2);";
        $tmp = tempnam(sys_get_temp_dir(), 'pel_');
        file_put_contents($tmp, $code);

        try {
            $ok = (bool) (include $tmp);
        } catch (Throwable) {
            $ok = false;
        }

        @unlink($tmp);
        $trace->add('policy', $ok ? 'allow' : 'deny', ['expr' => $expr]);

        return $ok ? Decision::allowed($trace, 'pel-allow') : Decision::denied($trace, 'pel-deny');
    }
}
