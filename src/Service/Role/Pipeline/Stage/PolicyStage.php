<?php
declare(strict_types=1);
/* Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp */

namespace Pipeline\Stage;

use App\Domain\Role\Model\RequestContext;
use App\Service\Role\Pipeline\{Stage};
use Pipeline\Decision;
use Pipeline\Trace;
use Throwable;

/**
 *
 */

/**
 *
 */
final class PolicyStage implements Stage
{
    /** @var array */
    private array $pol;

    /**
     * @param array $pol
     */
    public function __construct(array $pol)
    {
        $this->pol = $pol;
    }

    /**
     * @param \App\Domain\Role\Model\RequestContext $ctx
     * @param \Pipeline\Trace $trace
     * @return \Pipeline\Decision|null
     */
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
        foreach ($ctx->attrs as $k => $v) {
            $map['attrs.' . $k] = is_scalar($v) ? (string)$v : json_encode($v);
        }
        $expr2 = str_replace([' and ', ' or '], [' && ', ' || '], $expr);
        $expr2 = preg_replace('/\b([A-Za-z0-9_\-\.]+)\s+in\s+\[/', 'in_array(\1, [', $expr2);
        $expr2 = preg_replace_callback('/\b(subject\.role|action|resource\.type|attrs\.[A-Za-z0-9_\-]+)\b/', function (array $m) use ($map) {
            $k = $m[0];
            $v = $map[$k] ?? null;
            if (is_string($v)) return "'" . str_replace("'", "\\'", $v) . "'";
            if (is_bool($v)) return $v ? 'true' : 'false';
            if (is_numeric($v)) return (string)$v;
            return 'null';
        }, $expr2);
        if (preg_match('/[^A-Za-z0-9_\-\(\)\[\]\,\'\"\s\&\|\!\.]/', $expr2)) {
            $trace->add('policy', 'invalid');
            return null;
        }
        $code = "<?php\nreturn (bool)($expr2);";
        $tmp = tempnam(sys_get_temp_dir(), 'pel_');
        file_put_contents($tmp, $code);
        try {
            $ok = (bool)(include $tmp);
        } catch (Throwable $t) {
            $ok = false;
        }
        @unlink($tmp);
        $trace->add('policy', $ok ? 'allow' : 'deny', ['expr' => $expr]);
        return $ok ? Decision::allowed($trace, 'pel-allow') : Decision::denied($trace, 'pel-deny');
    }
}
