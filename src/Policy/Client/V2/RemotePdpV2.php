<?php

declare(strict_types=1);

namespace App\Rolling\Policy\Client\V2;

use App\Rolling\Entity\Role\PermissionKey;
use App\Rolling\Entity\Role\Scope;
use App\Rolling\Entity\Role\SubjectId;
use App\Rolling\Net\Http\SimpleHttpClientInterface;
use App\Rolling\Policy\Obligation\Obligations;
use App\Rolling\Policy\V2\DecisionWithObligations;
use App\Rolling\ServiceInterface\Policy\PdpV2Interface;

final class RemotePdpV2 implements PdpV2Interface
{
    public function __construct(private readonly string $baseUrl, private readonly SimpleHttpClientInterface $http, private readonly ?string $apiKey = null, private readonly ?string $hmac = null, private readonly int $timeoutMs = 300, private readonly int $retries = 0, private $cb = null)
    {
    }

    public function check(SubjectId $s, PermissionKey $a, Scope $sc, array $c = []): DecisionWithObligations
    {
        return DecisionWithObligations::deny('remote_pdp_unavailable', Obligations::empty());
    }
}
