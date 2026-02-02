<?php
declare(strict_types=1);

namespace SmartResponsor\RoleSdk\V2;

/** @psalm-type AccessCheckRequest = array{
 *   subjectId: string,
 *   action: string,
 *   scopeType: 'global'|'tenant'|'resource',
 *   tenantId?: string,
 *   resourceId?: string,
 *   context?: array<string, mixed>
 * }
 */
final class Types
{
    /** @return array<string,mixed> */
    public static function accessCheck(string $subjectId, string $action, string $scopeType, ?string $tenantId = null, ?string $resourceId = null, array $context = []): array
    {
        $req = ['subjectId' => $subjectId, 'action' => $action, 'scopeType' => $scopeType];
        if ($tenantId !== null) $req['tenantId'] = $tenantId;
        if ($resourceId !== null) $req['resourceId'] = $resourceId;
        if ($context !== []) $req['context'] = $context;
        return $req;
    }
}
