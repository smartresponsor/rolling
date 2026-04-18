<?php

declare(strict_types=1);

namespace App\Integration\Http\V2;

use App\Entity\Role\PermissionKey;
use App\Entity\Role\Scope;
use App\Entity\Role\SubjectId;
use App\ServiceInterface\Policy\PdpV2Interface;

final class ApiV2
{
    public function __construct(private readonly PdpV2Interface $pdp) {}

    /**
     * @param array<string,mixed> $in
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
        $body = json_encode([
            'decision' => $dec->isAllow() ? 'ALLOW' : 'DENY',
            'reason' => $dec->reason(),
            'obligations' => $dec->obligations()->toArray(),
            'scope' => $sc->key(),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return new Response(200, ['Content-Type' => 'application/json'], $body ?: '{}');
    }
}
