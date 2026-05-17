<?php

declare(strict_types=1);

namespace App\Rolling\Controller\Api;

use App\Rolling\Service\Context\EnvironmentVariableContextReader;
use App\Rolling\Service\Context\HeaderRequestContextReader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class ContextController
{
    /**
     * @param Request $r
     *
     * @return JsonResponse
     */
    public function capture(Request $r): JsonResponse
    {
        $h = (new HeaderRequestContextReader())->capture($r);
        $e = (new EnvironmentVariableContextReader())->capture();

        return new JsonResponse(['attrs' => array_merge($e, $h)], 200);
    }
}
