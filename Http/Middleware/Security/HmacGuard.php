<?php

declare(strict_types=1);

namespace Http\Middleware\Security;

use Http\Response;
use Http\Security\HmacVerifier;
use Http\Security\Replay\StoreInterface;
use Http\Server\HandlerInterface;
use Http\Server\MiddlewareInterface;
use Http\Server\Request;

final class HmacGuard implements MiddlewareInterface
{
    public function __construct(
        private readonly HmacVerifier   $verifier,
        private readonly StoreInterface $replayStore,
        private readonly string         $path = '/v2/access/check',
        private readonly int            $nonceTtlSec = 600,
    ) {}

    public function process(Request $request, HandlerInterface $handler): Response
    {
        if (!$this->shouldHandle($request)) {
            return $handler->handle($request);
        }

        $failure = $this->verifyRequest($request);
        if ($failure !== null) {
            return $failure;
        }

        $failure = $this->protectAgainstReplay($request);
        if ($failure !== null) {
            return $failure;
        }

        return $handler->handle($request);
    }

    private function shouldHandle(Request $request): bool
    {
        return $request->path() === $this->path;
    }

    private function verifyRequest(Request $request): ?Response
    {
        $date = $request->header('Date') ?? '';
        $body = $request->body();
        $result = $this->verifier->verify(
            $request->method(),
            $this->path,
            $date,
            $body,
            $request->header('X-Signature') ?? '',
        );

        if ($result['ok']) {
            return null;
        }

        return $this->unauthorized($result['reason'] ?? 'unauthorized');
    }

    private function protectAgainstReplay(Request $request): ?Response
    {
        $body = $request->body();
        $date = $request->header('Date') ?? '';
        $nonce = $request->header('X-Nonce') ?? HmacVerifier::derivedNonce($date, $body);

        if ($this->replayStore->seen($nonce, $this->nonceTtlSec)) {
            return null;
        }

        return $this->unauthorized('replay');
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
