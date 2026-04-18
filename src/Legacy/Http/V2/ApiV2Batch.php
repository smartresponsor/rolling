<?php

declare(strict_types=1);

namespace App\Legacy\Http\V2;

<<<<<<< HEAD:src/Legacy/Http/V2/ApiV2Batch.php
use App\Http\Role\V2\Response;
use App\Legacy\PolicyInterface\PdpV2Interface;
=======
use PolicyInterface\Role\PdpV2Interface;
>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa:Http/Role/V2/ApiV2Batch.php

/**
 *
 */

/**
 *
 */
final class ApiV2Batch
{
    /**
     * @param \PolicyInterface\Role\PdpV2Interface $pdp
     */
    public function __construct(private readonly PdpV2Interface $pdp) {}

    /**
     * @param array $in
     * @return \Http\Role\V2\Response
     */
    public function checkBatch(array $in): Response
    {
        $reqs = (array) ($in['requests'] ?? []);
        $results = [];
        $single = new ApiV2($this->pdp);
        foreach ($reqs as $r) {
            $res = $single->check((array) $r);
            $results[] = json_decode($res->body, true);
        }
        $body = json_encode(['count' => count($results), 'results' => $results], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return new Response(200, ['Content-Type' => 'application/json'], $body ?: '{}');
    }
}
