<?php
declare(strict_types=1);

namespace App\Legacy\Http\V2\Context;
/**
 *
 */

/**
 *
 */
final class ContextMerge
{
    /**
     * @param array $client @param array<string,mixed> $server @return array<string,mixed>
     * @param array $server
     * @return array
     */
    public static function merge(array $client, array $server): array
    {
        return $server + $client; // client wins on conflicts
    }
}
