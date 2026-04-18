<?php

declare(strict_types=1);

namespace App\Integration\Http\V2\Context;

final class ContextMerge
{
    /** @param array<string,mixed> $client @param array<string,mixed> $server @return array<string,mixed> */
    public static function merge(array $client, array $server): array
    {
        return self::mergeRecursive($client, $server);
    }

    /** @param array<string,mixed> $client @param array<string,mixed> $server @return array<string,mixed> */
    private static function mergeRecursive(array $client, array $server): array
    {
        $merged = $client;
        foreach ($server as $key => $value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = self::mergeRecursive($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }
}
