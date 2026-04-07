<?php
declare(strict_types=1);

namespace App\Legacy\Consistency;

use App\Legacy\Consistency\Policy\Token as PolicyToken;
use App\Legacy\Consistency\Rebac\Token as RebacToken;
use Closure;

/**
 *
 */

/**
 *
 */
final class Composer
{
    /**
     * @param \Closure $policyTokenFn
     * @param \Closure $rebacTokenFn
     * @param \Closure|null $subjectEpochFn
     */
    public function __construct(
        private readonly Closure  $policyTokenFn, // fn(): PolicyToken
        private readonly Closure  $rebacTokenFn,  // fn(): RebacToken
        private readonly ?Closure $subjectEpochFn = null // fn(string $subjectId): int
    )
    {
    }

    /**
     * @param string|null $subjectId
     * @return \App\Legacy\Consistency\TokenSet
     */
    public function token(?string $subjectId = null): TokenSet
    {
        /** @var PolicyToken $pt */
        $pt = ($this->policyTokenFn)();
        /** @var RebacToken $rt */
        $rt = ($this->rebacTokenFn)();
        $se = null;
        if ($subjectId !== null && $this->subjectEpochFn) {
            /** @var int $se */
            $se = ($this->subjectEpochFn)($subjectId);
        }
        return new TokenSet($pt->rev, $rt->rev, $se);
    }
}
