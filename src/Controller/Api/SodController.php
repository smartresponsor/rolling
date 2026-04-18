<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Service\Sod\SodGuard;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 */

/**
 *
 */
final class SodController
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $r
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function check(Request $r): JsonResponse
    {
        $p = json_decode((string) $r->getContent(), true) ?? [];
        $g = new SodGuard();
        return new JsonResponse($g->validate((array) ($p['attrs'] ?? [])), 200);
    }
}
