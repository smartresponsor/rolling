<?php

declare(strict_types=1);

namespace App\Legacy\Http\V2;

use App\Legacy\PolicyInterface\PdpV2Interface;
use App\Legacy\Entity\Role\{Scope};
use App\Entity\Role\PermissionKey;
use App\Entity\Role\SubjectId;

/**
 *
 */

/**
 *
 */
final class Response
{
    /**
     * @param int $status
     * @param array $headers
     * @param string $body
     */
    public function __construct(public int $status, public array $headers, public string $body) {}
}

/**
 *
 */

/**
 *
 */
final class ApiV2
{
    /**
     * @param \PolicyInterface\Role\PdpV2Interface $pdp
     */
    public function __construct(private readonly PdpV2Interface $pdp) {}

    /**
     * @param array $in
     * @return \Http\Role\V2\Response
     */
    public function check(array $in): Response
    {
        $sid = new SubjectId((string) ($in['subjectId'] ?? ''));
        $act = new PermissionKey((string) ($in['action'] ?? ''));
        $sc = match ($in['scopeType'] ?? 'global') {
            'tenant' => Scope::tenant((string) ($in['tenantId'] ?? '')),
            'resource' => Scope::resource((string) ($in['tenantId'] ?? ''), (string) ($in['resourceId'] ?? '')),
            default => Scope::global(),
        };
        $dec = $this->pdp->check($sid, $act, $sc, (array) ($in['context'] ?? []));
        $body = json_encode(['decision' => $dec->isAllow() ? 'ALLOW' : 'DENY', 'reason' => $dec->reason, 'obligations' => [], 'scope' => $sc->key()], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return new Response(200, ['Content-Type' => 'application/json'], $body ?: '{}');
    }
}
