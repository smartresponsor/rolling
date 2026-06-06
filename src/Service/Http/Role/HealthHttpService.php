<?php

declare(strict_types=1);

namespace App\Rolling\Service\Http\Role;

use Symfony\Component\HttpFoundation\JsonResponse;

final class HealthHttpService
{
    public function index(): JsonResponse
    {
        return new JsonResponse(['status' => 'ok']);
    }
}
