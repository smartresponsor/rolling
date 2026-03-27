<?php

declare(strict_types=1);

namespace Policy\Role\Client\V2;

use App\Net\Http\SimpleHttpClientInterface;
use Policy\Role\Obligation\Obligations;
use Policy\Role\V2\DecisionWithObligations;
use PolicyInterface\Role\PdpV2Interface;
use src\Entity\Role\{Scope};
use src\Entity\Role\PermissionKey;
use src\Entity\Role\SubjectId;

/**
 *
 */

/**
 *
 */
final class RemotePdpV2 implements PdpV2Interface
{
    /**
     * @param string $baseUrl
     * @param \App\Net\Http\SimpleHttpClientInterface $http
     * @param string|null $apiKey
     * @param string|null $hmac
     * @param int $timeoutMs
     * @param int $retries
     * @param $cb
     */
    /**
     * @param string $baseUrl
     * @param \App\Net\Http\SimpleHttpClientInterface $http
     * @param string|null $apiKey
     * @param string|null $hmac
     * @param int $timeoutMs
     * @param int $retries
     * @param $cb
     */
    public function __construct(private readonly string $baseUrl, private readonly SimpleHttpClientInterface $http, private readonly ?string $apiKey = null, private readonly ?string $hmac = null, private readonly int $timeoutMs = 300, private readonly int $retries = 0, private $cb = null) {}

    /**
     * @param \src\Entity\Role\SubjectId $s
     * @param \src\Entity\Role\PermissionKey $a
     * @param \src\Entity\Role\Scope $sc
     * @param array $c
     * @return \Policy\Role\V2\DecisionWithObligations
     */
    public function check(SubjectId $s, PermissionKey $a, Scope $sc, array $c = []): DecisionWithObligations
    {
        return DecisionWithObligations::allow('remote-placeholder', Obligations::empty());
    }
}
