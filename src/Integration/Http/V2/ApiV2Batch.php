<?php

declare(strict_types=1);

namespace App\Rolling\Integration\Http\V2;

use App\Rolling\ServiceInterface\Policy\PdpV2Interface;

final class ApiV2Batch
{
    public function __construct(private readonly PdpV2Interface $pdp)
    {
    }

    /**
     * @param array<string,mixed> $in
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
