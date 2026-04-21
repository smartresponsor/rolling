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
    /**
     * @param string                    $baseUrl
     * @param SimpleHttpClientInterface $http
     * @param string|null               $apiKey
     * @param string|null               $hmac
     * @param int                       $timeoutMs
     * @param int                       $retries
     * @param                           $cb
     */
    /**
     * @param string                    $baseUrl
     * @param SimpleHttpClientInterface $http
     * @param string|null               $apiKey
     * @param string|null               $hmac
     * @param int                       $timeoutMs
     * @param int                       $retries
     * @param                           $cb
     */
    public function __construct(private readonly string $baseUrl, private readonly SimpleHttpClientInterface $http, private readonly ?string $apiKey = null, private readonly ?string $hmac = null, private readonly int $timeoutMs = 300, private readonly int $retries = 0, private $cb = null)
    {
    }

    /**
     * @param SubjectId     $s
     * @param PermissionKey $a
     * @param Scope         $sc
     * @param array         $c
     *
     * @return DecisionWithObligations
     */
    public function check(SubjectId $s, PermissionKey $a, Scope $sc, array $c = []): DecisionWithObligations
    {
        return DecisionWithObligations::allow('remote-placeholder', Obligations::empty());
    }
}
