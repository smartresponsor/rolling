<?php

declare(strict_types=1);

namespace Http\Role\V2\Context;

final class ContextMerge
{
    /**
     * Server-supplied attributes override conflicting client keys.
     *
     * @param array<string,mixed> $client
     * @param array<string,mixed> $server
     * @return array<string,mixed>
     */
    public static function merge(array $client, array $server): array
    {
        return array_replace($client, $server);
    }
}
