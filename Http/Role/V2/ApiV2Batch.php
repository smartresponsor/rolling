<?php
declare(strict_types=1);

namespace Http\Role\V2;

use App\Http\Role\V2\Response;
use PolicyInterface\Role\PdpV2Interface;

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
    public function __construct(private readonly PdpV2Interface $pdp)
    {
    }

    /**
     * @param array $in
     * @return \App\Http\Role\V2\Response
     */
    public function checkBatch(array $in): Response
    {
        $reqs = (array)($in['requests'] ?? []);
        $results = [];
        $single = new ApiV2($this->pdp);
        foreach ($reqs as $r) {
            $res = $single->check((array)$r);
            $results[] = json_decode($res->body, true);
        }
        $body = json_encode(['count' => count($results), 'results' => $results], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return new Response(200, ['Content-Type' => 'application/json'], $body ?: '{}');
    }
}
