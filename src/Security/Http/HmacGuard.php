<?php

declare(strict_types=1);

namespace App\Security\Http;

use App\InfrastructureInterface\Security\ReplayNonceStoreInterface;

final class HmacGuard
{
    public function __construct(
        private readonly HmacRequestVerifier $verifier,
        private readonly ReplayNonceStoreInterface $replayStore,
        private readonly string $path = '/v2/access/check',
        private readonly int $nonceTtlSec = 600,
    ) {
    }

    public function process(RequestInterface $request, HandlerInterface $handler): Response
    {
        if ($request->path() !== $this->path) {
            return $handler->handle($request);
        }

        $date = $request->header('Date') ?? '';
        $body = $request->body();
        $result = $this->verifier->verify(
            $request->method(),
            $this->path,
            $date,
            $body,
            $request->header('X-Signature') ?? '',
        );

        if (($result['ok'] ?? false) !== true) {
            return $this->unauthorized((string) ($result['reason'] ?? 'unauthorized'));
        }

        $nonce = $request->header('X-Nonce') ?? HmacRequestVerifier::derivedNonce($date, $body);
        if (!$this->replayStore->seen($nonce, $this->nonceTtlSec)) {
            return $this->unauthorized('replay');
        }

        return $handler->handle($request);
    }

    private function unauthorized(string $reason): Response
    {
        return new Response(
            401,
            [
                'Content-Type' => 'application/json',
                'X-Auth-Error' => $reason,
            ],
            (string) json_encode(
                ['error' => 'unauthorized', 'reason' => $reason],
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
            ),
        );
    }
}
