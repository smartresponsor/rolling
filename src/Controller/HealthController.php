<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

final class HealthController
{
    public function index(): JsonResponse
    {
        return new JsonResponse(['status' => 'ok']);
    }
}
