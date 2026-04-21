<?php

declare(strict_types=1);

namespace App\Rolling\Controller\Api;

use App\Rolling\Service\Sod\SodGuard;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class SodController
{
    /**
     * @param Request $r
     *
     * @return JsonResponse
     */
    public function check(Request $r): JsonResponse
    {
        $p = json_decode((string) $r->getContent(), true) ?? [];
        $g = new SodGuard();

        return new JsonResponse($g->validate((array) ($p['attrs'] ?? [])), 200);
    }
}
