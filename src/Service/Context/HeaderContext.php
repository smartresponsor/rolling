<?php

declare(strict_types=1);

namespace App\Rolling\Service\Context;

use Symfony\Component\HttpFoundation\Request;

final class HeaderContext
{
    /** @return array<string,mixed> */
    public function capture(Request $request): array
    {
        $role = (string) ($request->headers->get('X-Role') ?? '');
        $region = (string) ($request->headers->get('X-Region') ?? '');
        $context = [];

        if ('' !== $role) {
            $context['role'] = $role;
        }
        if ('' !== $region) {
            $context['region'] = $region;
        }

        return $context;
    }
}
