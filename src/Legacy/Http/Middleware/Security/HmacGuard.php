<?php

declare(strict_types=1);

namespace App\Legacy\Http\Middleware\Security;

<<<<<<< HEAD:src/Legacy/Http/Middleware/Security/HmacGuard.php
use App\Legacy\Http\Server\MiddlewareInterface;
use App\Legacy\Http\Response;
use App\Security\Http\HmacRequestVerifier;
use App\InfrastructureInterface\Security\ReplayNonceStoreInterface;
use App\Legacy\Http\Server\HandlerInterface;
use App\Legacy\Http\Server\Request;
=======
use Http\Response;
use Http\Security\HmacVerifier;
use Http\Security\Replay\StoreInterface;
use Http\Server\HandlerInterface;
use Http\Server\MiddlewareInterface;
use Http\Server\Request;
>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa:Http/Middleware/Security/HmacGuard.php

final class HmacGuard implements MiddlewareInterface
{
<<<<<<< HEAD:src/Legacy/Http/Middleware/Security/HmacGuard.php
    /**
     * @param \Http\Security\HmacRequestVerifier $verifier
     * @param \Http\Security\Replay\ReplayNonceStoreInterface $replayStore
     * @param string $path
     * @param int $nonceTtlSec
     */
=======
>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa:Http/Middleware/Security/HmacGuard.php
    public function __construct(
        private readonly HmacRequestVerifier $verifier,
        private readonly ReplayNonceReplayNonceStoreInterface $replayStore,
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

<<<<<<< HEAD:src/Legacy/Http/Middleware/Security/HmacGuard.php
        // Anti-replay
        $nonce = $request->header('X-Nonce') ?? HmacRequestVerifier::derivedNonce($date, $body);
        if (!$this->replayStore->seen($nonce, $this->nonceTtlSec)) {
            return new Response(401, ['Content-Type' => 'application/json', 'X-Auth-Error' => 'replay'], json_encode(['error' => 'unauthorized', 'reason' => 'replay'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
=======
        $failure = $this->protectAgainstReplay($request);
        if ($failure !== null) {
            return $failure;
>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa:Http/Middleware/Security/HmacGuard.php
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
