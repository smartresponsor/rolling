<?php

declare(strict_types=1);

<<<<<<< HEAD:src/Legacy/Http/V2/Context/ContextMerge.php
namespace App\Legacy\Http\V2\Context;
/**
 *
 */
=======
namespace Http\Role\V2\Context;
>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa:Http/Role/V2/Context/ContextMerge.php

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
