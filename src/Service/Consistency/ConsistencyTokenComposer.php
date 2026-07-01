<?php

declare(strict_types=1);

namespace App\Rolling\Service\Consistency;

use App\Rolling\Service\Consistency\Policy\PolicyConsistencyToken;
use App\Rolling\Service\Consistency\Rebac\RebacConsistencyToken;

final class ConsistencyTokenComposer
{
    public function __construct(
        private readonly ?\Closure $policyTokenFn = null, // fn(): PolicyConsistencyToken
        private readonly ?\Closure $rebacTokenFn = null,  // fn(): RebacConsistencyToken
        private readonly ?\Closure $subjectEpochFn = null, // fn(string $subjectId): int
    ) {
    }

    public function token(?string $subjectId = null): ConsistencyTokenSet
    {
        /** @var PolicyConsistencyToken $pt */
        $pt = $this->policyTokenFn instanceof \Closure ? ($this->policyTokenFn)() : new PolicyConsistencyToken(0);
        /** @var RebacConsistencyToken $rt */
        $rt = $this->rebacTokenFn instanceof \Closure ? ($this->rebacTokenFn)() : new RebacConsistencyToken(0);
        $se = null;
        if (null !== $subjectId && $this->subjectEpochFn) {
            /** @var int $se */
            $se = ($this->subjectEpochFn)($subjectId);
        }

        return new ConsistencyTokenSet($pt->rev, $rt->rev, $se);
    }
}
