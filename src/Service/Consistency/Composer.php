<?php

declare(strict_types=1);

namespace App\Rolling\Service\Consistency;

use App\Rolling\Service\Consistency\Policy\Token as PolicyToken;
use App\Rolling\Service\Consistency\Rebac\Token as RebacToken;

final class Composer
{
    /**
     * @param \Closure      $policyTokenFn
     * @param \Closure      $rebacTokenFn
     * @param \Closure|null $subjectEpochFn
     */
    public function __construct(
        private readonly ?\Closure $policyTokenFn = null, // fn(): PolicyToken
        private readonly ?\Closure $rebacTokenFn = null,  // fn(): RebacToken
        private readonly ?\Closure $subjectEpochFn = null, // fn(string $subjectId): int
    ) {
    }

    /**
     * @param string|null $subjectId
     *
     * @return TokenSet
     */
    public function token(?string $subjectId = null): TokenSet
    {
        /** @var PolicyToken $pt */
        $pt = $this->policyTokenFn instanceof \Closure ? ($this->policyTokenFn)() : new PolicyToken(0);
        /** @var RebacToken $rt */
        $rt = $this->rebacTokenFn instanceof \Closure ? ($this->rebacTokenFn)() : new RebacToken(0);
        $se = null;
        if (null !== $subjectId && $this->subjectEpochFn) {
            /** @var int $se */
            $se = ($this->subjectEpochFn)($subjectId);
        }

        return new TokenSet($pt->rev, $rt->rev, $se);
    }
}
