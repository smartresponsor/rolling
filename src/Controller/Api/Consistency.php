<?php

declare(strict_types=1);

namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class Consistency
{
    public static function mode(Request $request): string
    {
        return (string) ($request->headers->get('X-Consistency') ?? 'eventual');
    }

    public static function applyHeaders(JsonResponse $response, string $mode, string $token): void
    {
        $response->headers->set('X-Consistency', $mode);
        $response->headers->set('X-Consistency-Token', $token);
    }
}
