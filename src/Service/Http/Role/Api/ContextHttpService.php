<?php

declare(strict_types=1);

namespace App\Rolling\Service\Http\Role\Api;

use App\Rolling\Service\Context\EnvironmentVariableContextReader;
use App\Rolling\Service\Context\HeaderRequestContextReader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class ContextHttpService
{
    public function capture(Request $r): JsonResponse
    {
        $h = (new HeaderRequestContextReader())->capture($r);
        $e = (new EnvironmentVariableContextReader())->capture();

        return new JsonResponse(['attrs' => array_merge($e, $h)], 200);
    }
}
