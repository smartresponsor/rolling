<?php

declare(strict_types=1);

namespace App\Policy\Client\V2;

use App\Net\Http\SimpleHttpClientInterface;
use App\Policy\Obligation\Obligations;
use App\Policy\V2\DecisionWithObligations;
use App\ServiceInterface\Policy\PdpV2Interface;
use App\Entity\Role\Scope;
use App\Entity\Role\PermissionKey;
use App\Entity\Role\SubjectId;

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
     * @param \App\Entity\Role\SubjectId $s
     * @param \App\Entity\Role\PermissionKey $a
     * @param \App\Entity\Role\Scope $sc
     * @param array $c
     * @return \App\Policy\V2\DecisionWithObligations
     */
    public function check(SubjectId $s, PermissionKey $a, Scope $sc, array $c = []): DecisionWithObligations
    {
        return DecisionWithObligations::allow('remote-placeholder', Obligations::empty());
    }
}
