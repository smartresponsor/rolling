<?php

declare(strict_types=1);

namespace App\Legacy\Policy\V2;

use App\Legacy\Shadow\Diff\DecisionDiff;
use App\Legacy\Shadow\Report\DiffReporterInterface;
use App\Legacy\Shadow\Sampler\PercentageSampler;
use App\Legacy\PolicyInterface\PdpV2Interface;
use App\Legacy\Entity\Role\{Scope};
use App\Entity\Role\PermissionKey;
use App\Entity\Role\SubjectId;
use Throwable;

/**
 *
 */

/**
 *
 */
final class ShadowPdpV2 implements PdpV2Interface
{
    /**
     * @param \PolicyInterface\Role\PdpV2Interface $live
     * @param \PolicyInterface\Role\PdpV2Interface $shadow
     * @param \App\Legacy\Shadow\Sampler\PercentageSampler $sampler
     * @param \App\Legacy\Shadow\Report\DiffReporterInterface $reporter
     * @param bool $alwaysRunOnHeader
     */
    public function __construct(private readonly PdpV2Interface $live, private readonly PdpV2Interface $shadow, private readonly PercentageSampler $sampler, private readonly DiffReporterInterface $reporter, private readonly bool $alwaysRunOnHeader = true) {}

    /**
     * @param \App\Entity\Role\SubjectId $s
     * @param \App\Entity\Role\PermissionKey $a
     * @param \App\Entity\Role\Scope $sc
     * @param array $context
     * @return \Policy\Role\V2\DecisionWithObligations
     */
    public function check(SubjectId $s, PermissionKey $a, Scope $sc, array $context = []): DecisionWithObligations
    {
        $liveDec = $this->live->check($s, $a, $sc, $context);
        $force = (bool) ($context['_force_shadow'] ?? false);
        $key = $s . '|' . $a . '|' . $sc->key();
        if ($force || $this->sampler->hit($key)) {
            try {
                $shadowDec = $this->shadow->check($s, $a, $sc, $context);
                $diff = DecisionDiff::diff($liveDec, $shadowDec);
                if (!$diff['equal']) {
                    $this->reporter->report(['type' => 'shadow_diff', 'subject' => (string) $s, 'action' => (string) $a, 'scope' => $sc->key(), 'diff' => $diff,]);
                }
            } catch (Throwable $e) {
                $this->reporter->report(['type' => 'shadow_error', 'subject' => (string) $s, 'action' => (string) $a, 'scope' => $sc->key(), 'error' => $e->getMessage(),]);
            }
        }
        return $liveDec;
    }
}
