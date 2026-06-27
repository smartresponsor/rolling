<?php

declare(strict_types=1);

namespace App\Rolling\Service\Http\Role\Api\Admin;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final readonly class VendorAdminHttpService
{
    public function __construct(private TenantAdminHttpService $inner)
    {
    }

    public function quotaGet(Request $request): JsonResponse
    {
        return $this->inner->quotaGet($request);
    }

    public function quotaSet(Request $request): JsonResponse
    {
        return $this->inner->quotaSet($request);
    }

    public function backup(Request $request): JsonResponse
    {
        return $this->inner->backup($request);
    }

    public function restore(Request $request): JsonResponse
    {
        return $this->inner->restore($request);
    }
}
