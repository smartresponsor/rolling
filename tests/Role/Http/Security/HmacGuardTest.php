<?php
declare(strict_types=1);

namespace Tests\Role\Http\Security;

use App\Legacy\Http\Middleware\Security\HmacGuard;
use App\Legacy\Http\Response;
use App\Security\Http\HmacRequestVerifier;
use App\InfrastructureInterface\Security\ReplayNonceStoreInterface;
use App\Legacy\Http\Server\HandlerInterface;
use App\Legacy\Http\Server\Request;
use PHPUnit\Framework\TestCase;

final class HmacGuardTest extends TestCase
{
    public function testOkPassesToHandler(): void
    {
        $date = gmdate('D, d M Y H:i:s \\G\\M\\T');
        $body = '{"a":1}';
        $base = 'POST /v2/access/check' . "\n" . $date . "\n" . $body;
        $sig = 'v1=' . base64_encode(hash_hmac('sha256', $base, 'k', true));
        $req = new class (['Date' => $date, 'X-Signature' => $sig], $body) implements Request {
            public function __construct(private readonly array $headers, private readonly string $bodyContent)
            {
            }

            public function method(): string
            {
                return 'POST';
            }

            public function path(): string
            {
                return '/v2/access/check';
            }

            public function header(string $name): ?string
            {
                return $this->headers[$name] ?? null;
            }

            public function body(): string
            {
                return $this->bodyContent;
            }
        };

        $store = new class implements ReplayNonceStoreInterface {
            private array $seen = [];

            public function seen(string $nonce, int $ttlSec): bool
            {
                if (isset($this->seen[$nonce])) {
                    return false;
                }

                $this->seen[$nonce] = time() + $ttlSec;

                return true;
            }
        };

        $handler = new class implements HandlerInterface {
            public function handle(Request $request): Response
            {
                return new Response(200, ['Content-Type' => 'application/json'], '{"ok":1}');
            }
        };

        $guard = new HmacGuard(new HmacRequestVerifier('k'), $store);
        $response = $guard->process($req, $handler);

        $this->assertSame(200, $response->status);
    }

    public function testReplayBlocked(): void
    {
        $date = gmdate('D, d M Y H:i:s \\G\\M\\T');
        $body = '{"a":1}';
        $base = 'POST /v2/access/check' . "\n" . $date . "\n" . $body;
        $sig = 'v1=' . base64_encode(hash_hmac('sha256', $base, 'k', true));
        $req = new class (['Date' => $date, 'X-Signature' => $sig], $body) implements Request {
            public function __construct(private readonly array $headers, private readonly string $bodyContent)
            {
            }

            public function method(): string
            {
                return 'POST';
            }

            public function path(): string
            {
                return '/v2/access/check';
            }

            public function header(string $name): ?string
            {
                return $this->headers[$name] ?? null;
            }

            public function body(): string
            {
                return $this->bodyContent;
            }
        };

        $store = new class implements ReplayNonceStoreInterface {
            private array $seen = [];

            public function seen(string $nonce, int $ttlSec): bool
            {
                if (isset($this->seen[$nonce])) {
                    return false;
                }

                $this->seen[$nonce] = time() + $ttlSec;

                return true;
            }
        };

        $handler = new class implements HandlerInterface {
            public function handle(Request $request): Response
            {
                return new Response(200, ['Content-Type' => 'application/json'], '{"ok":1}');
            }
        };

        $guard = new HmacGuard(new HmacRequestVerifier('k'), $store);
        $guard->process($req, $handler);
        $response = $guard->process($req, $handler);

        $this->assertSame(401, $response->status);
    }
}
