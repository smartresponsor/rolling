<?php

declare(strict_types=1);
/* Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp */

namespace Context;

use Symfony\Component\HttpFoundation\Request;

/**
 *
 */

/**
 *
 */
final class HeaderContext
{
    /** @return array<string,mixed> */
    public function capture(Request $r): array
    {
        $role = (string) ($r->headers->get('X-Role') ?? '');
        $region = (string) ($r->headers->get('X-Region') ?? '');
        $out = [];
        if ($role !== '') {
            $out['role'] = $role;
        }
        if ($region !== '') {
            $out['region'] = $region;
        }
        return $out;
    }
}
