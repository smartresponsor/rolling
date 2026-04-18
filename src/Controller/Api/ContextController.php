<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Legacy\Service\Context\{HeaderContext};
use App\Legacy\Service\Context\EnvContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 */

/**
 *
 */
final class ContextController
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $r
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function capture(Request $r): JsonResponse
    {
        $h = (new HeaderContext())->capture($r);
        $e = (new EnvContext())->capture();
        return new JsonResponse(['attrs' => array_merge($e, $h)], 200);
    }
}
