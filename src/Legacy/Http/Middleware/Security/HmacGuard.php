<?php
declare(strict_types=1);

namespace App\Legacy\Http\Middleware\Security;

use App\Legacy\Http\Server\MiddlewareInterface;
use App\Legacy\Http\Response;
use App\Security\Http\HmacRequestVerifier;
use App\InfrastructureInterface\Security\ReplayNonceStoreInterface;
use App\Legacy\Http\Server\HandlerInterface;
use App\Legacy\Http\Server\Request;

/**
 *
 */

/**
 *
 */
final class HmacGuard implements MiddlewareInterface
{
    /**
     * @param \Http\Security\HmacRequestVerifier $verifier
     * @param \Http\Security\Replay\ReplayNonceStoreInterface $replayStore
     * @param string $path
     * @param int $nonceTtlSec
     */
    public function __construct(
        private readonly HmacRequestVerifier $verifier,
        private readonly ReplayNonceReplayNonceStoreInterface $replayStore,
        private readonly string         $path = '/v2/access/check',
        private readonly int            $nonceTtlSec = 600
    )
    {
    }

    /**
     * @param \Http\Server\Request $request
     * @param \Http\Server\HandlerInterface $handler
     * @return \Http\Response
     */
    public function process(Request $request, HandlerInterface $handler): Response
    {
        if ($request->path() !== $this->path) {
            return $handler->handle($request);
        }

        $date = $request->header('Date') ?? '';
        $sig = $request->header('X-Signature') ?? '';
        $body = $request->body();
        $res = $this->verifier->verify($request->method(), $this->path, $date, $body, $sig);
        if (!$res['ok']) {
            return new Response(401, ['Content-Type' => 'application/json', 'X-Auth-Error' => $res['reason'] ?? 'unauthorized'], json_encode(['error' => 'unauthorized', 'reason' => $res['reason'] ?? ''], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }

        // Anti-replay
        $nonce = $request->header('X-Nonce') ?? HmacRequestVerifier::derivedNonce($date, $body);
        if (!$this->replayStore->seen($nonce, $this->nonceTtlSec)) {
            return new Response(401, ['Content-Type' => 'application/json', 'X-Auth-Error' => 'replay'], json_encode(['error' => 'unauthorized', 'reason' => 'replay'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }

        return $handler->handle($request);
    }
}
