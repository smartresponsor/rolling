<?php

declare(strict_types=1);

namespace App\Service\Pipeline;

/**
 * Canonical pipeline request context for in-process decision evaluation.
 */
final class RequestContext
{
    /**
     * @param array<string,mixed> $resource
     * @param array<string,mixed> $attrs
     */
    public function __construct(
        public string $tenant,
        public string $subject,
        public string $action,
        public array $resource = [],
        public array $attrs = [],
    ) {}
}
