<?php

declare(strict_types=1);

namespace App\Rolling\Controller\Api;

use App\Rolling\Infrastructure\Residency\ResidencyFsPolicy;
use App\Rolling\Service\Residency\ResidencyGuard;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class ResidencyController
{
    /**
     * @param string $conf
     */
    public function __construct(private readonly string $conf = __DIR__.'/../../../../config/role/residency.json')
    {
    }

    /**
     * @return ResidencyGuard
     */
    private function guard(): ResidencyGuard
    {
        return new ResidencyGuard(new ResidencyFsPolicy($this->conf));
    }

    /**
     * @param Request $req
     *
     * @return JsonResponse
     */
    public function enforce(Request $req): JsonResponse
    {
        $p = json_decode((string) $req->getContent(), true) ?? [];
        $tenant = (string) ($p['tenant'] ?? 't1');
        $attrs = (array) ($p['attrs'] ?? []);
        $action = (string) ($p['action'] ?? 'read');
        $res = $this->guard()->enforce($tenant, $attrs);

        return new JsonResponse($res, 200);
    }
}
