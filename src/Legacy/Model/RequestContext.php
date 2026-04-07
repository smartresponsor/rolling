<?php
declare(strict_types=1);
/* Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp */

namespace App\Legacy\Model;

/**
 *
 */

/**
 *
 */
final class RequestContext
{
    /**
     * @param string $tenant
     * @param string $subject
     * @param string $action
     * @param array $resource
     * @param array $attrs
     */
    public function __construct(
        public string $tenant,
        public string $subject,
        public string $action,
        /** @var array<string,mixed> */
        public array  $resource = [],
        /** @var array<string,mixed> */
        public array  $attrs = []
    )
    {
    }
}
