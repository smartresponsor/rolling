<?php

declare(strict_types=1);

namespace App\Rolling\Service\Http\Role\Api\Admin;

use App\Rolling\DTO\Http\Role\Admin\TenantBackupPayload;
use App\Rolling\DTO\Http\Role\Admin\TenantQuotaSetPayload;
use App\Rolling\DTO\Http\Role\Admin\TenantRestorePayload;
use App\Rolling\Security\Admin\Roles;
use App\Rolling\Security\Admin\Voter;
use App\Rolling\Service\Http\Request\JsonPayloadReader;
use App\Rolling\Service\Tenant\TenantBackupArchiveRestorer;
use App\Rolling\Service\Tenant\TenantBackupArchiveWriter;
use App\Rolling\Service\Tenant\TenantLimitConfigurationService;
use App\Rolling\Service\Tenant\TenantRequestQuotaService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class TenantAdminHttpService
{
    private Voter $voter;
    private TenantRequestQuotaService $quota;
    private TenantLimitConfigurationService $limits;
    private TenantBackupArchiveWriter $backup;
    private TenantBackupArchiveRestorer $restore;

    /**
     * @param string $secretPath
     * @param string $varDir
     */
    public function __construct(
        private readonly JsonPayloadReader $payloadReader,
        string $secretPath = __DIR__.'/../../../../../var/admin_secret.txt',
        string $varDir = __DIR__.'/../../../../../var',
    ) {
        $this->voter = new Voter($secretPath);
        $this->quota = new TenantRequestQuotaService($varDir.'/tenants');
        $this->limits = new TenantLimitConfigurationService($varDir.'/tenants');
        $this->backup = new TenantBackupArchiveWriter($varDir, $varDir.'/backup');
        $this->restore = new TenantBackupArchiveRestorer($varDir);
    }

    /**
     * @param Request $req
     *
     * @return JsonResponse
     */
    public function quotaGet(Request $req): JsonResponse
    {
        if (!$this->voter->isAdmin($req)) {
            return new JsonResponse(['ok' => false, 'error' => 'forbidden'], 403);
        }
        $tenant = (string) ($req->query->get('tenant') ?? '');
        if ('' === $tenant) {
            return new JsonResponse(['ok' => false, 'error' => 'tenant required'], 400);
        }
        $limit = $this->quota->getLimit($tenant);

        return new JsonResponse(['ok' => true, 'tenant' => $tenant, 'limit' => $limit]);
    }

    /**
     * @param Request $req
     *
     * @return JsonResponse
     */
    public function quotaSet(Request $req): JsonResponse
    {
        if (!$this->voter->isAdmin($req)) {
            return new JsonResponse(['ok' => false, 'error' => 'forbidden'], 403);
        }
        $payload = TenantQuotaSetPayload::fromArray($this->payloadReader->readObject($req));
        if ('' === $payload->tenant || $payload->perMinute <= 0) {
            return new JsonResponse(['ok' => false, 'error' => 'tenant/per_min required'], 400);
        }
        $this->quota->setLimit($payload->tenant, $payload->perMinute);

        return new JsonResponse(['ok' => true, 'tenant' => $payload->tenant, 'limit' => ['limit_per_min' => $payload->perMinute]]);
    }

    /**
     * @param Request $req
     *
     * @return JsonResponse
     */
    public function backup(Request $req): JsonResponse
    {
        if (!$this->voter->isAdmin($req)) {
            return new JsonResponse(['ok' => false, 'error' => 'forbidden'], 403);
        }
        $payload = TenantBackupPayload::fromArray($this->payloadReader->readObject($req));
        if ('' === $payload->tenant) {
            return new JsonResponse(['ok' => false, 'error' => 'tenant required'], 400);
        }
        $res = $this->backup->run($payload->tenant);

        return new JsonResponse($res, $res['ok'] ? 200 : 500);
    }

    /**
     * @param Request $req
     *
     * @return JsonResponse
     */
    public function restore(Request $req): JsonResponse
    {
        if (!$this->voter->isAdmin($req, [Roles::OWNER])) {
            return new JsonResponse(['ok' => false, 'error' => 'forbidden'], 403);
        }
        $payload = TenantRestorePayload::fromArray($this->payloadReader->readObject($req));
        if ('' === $payload->path || !file_exists($payload->path)) {
            return new JsonResponse(['ok' => false, 'error' => 'valid path required'], 400);
        }
        $res = $this->restore->run($payload->path);

        return new JsonResponse($res, $res['ok'] ? 200 : 500);
    }
}
