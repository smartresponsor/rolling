<?php

declare(strict_types=1);

namespace Tests\Role\Http\Security;

use Http\Middleware\Security\HmacGuard;
use Http\Response;
use Http\Security\HmacVerifier;
use Http\Security\Replay\StoreInterface;
use Http\Server\HandlerInterface;
use Http\Server\Request;
use PHPUnit\Framework\TestCase;

/**
 *
 */

/**
 *
 */
final class HmacGuardTest extends TestCase
{
    public function testOkPassesToHandler(): void
    {
        $date = gmdate('D, d M Y H:i:s \G\M\T');
        $body = '{"a":1}';
        $base = 'POST /v2/access/check' . "\n" . $date . "\n" . $body;
        $sig = 'v1=' . base64_encode(hash_hmac('sha256', $base, 'k', true));
        $req = new class (['Date' => $date, 'X-Signature' => $sig], $body) implements Request {
            public function __construct(private readonly array $h, private readonly string $body) {}

            public function method(): string
            {
                return 'POST';
            }

            public function path(): string
            {
                return '/v2/access/check';
            }

            public function header(string $n): ?string
            {
                return $this->h[$n] ?? null;
            }

            public function body(): string
            {
                return $this->body;
            }
        };

        $store = new class implements StoreInterface {
            private array $seen = [];

            public function seen(string $n, int $ttl): bool
            {
                if (isset($this->seen[$n])) {
                    return false;
                }

                $this->seen[$n] = time() + $ttl;

                return true;
            }
        };
        $handler = new class implements HandlerInterface {
            public function handle(Request $r): Response
            {
                return new Response(200, ['Content-Type' => 'application/json'], '{"ok":1}');
            }
        };

        $g = new HmacGuard(new HmacVerifier('k'), $store);
        $res = $g->process($req, $handler);
        $this->assertSame(200, $res->status);
    }

    public function testReplayBlocked(): void
    {
        $date = gmdate('D, d M Y H:i:s \G\M\T');
        $body = '{"a":1}';
        $base = 'POST /v2/access/check' . "\n" . $date . "\n" . $body;
        $sig = 'v1=' . base64_encode(hash_hmac('sha256', $base, 'k', true));
        $req = new class (['Date' => $date, 'X-Signature' => $sig], $body) implements Request {
            public function __construct(private readonly array $h, private readonly string $body) {}

            public function method(): string
            {
                return 'POST';
            }

            public function path(): string
            {
                return '/v2/access/check';
            }

            public function header(string $n): ?string
            {
                return $this->h[$n] ?? null;
            }

            public function body(): string
            {
                return $this->body;
            }
        };

        $store = new class implements StoreInterface {
            private array $seen = [];

            public function seen(string $n, int $ttl): bool
            {
                if (isset($this->seen[$n])) {
                    return false;
                }

                $this->seen[$n] = time() + $ttl;

                return true;
            }
        };
        $handler = new class implements HandlerInterface {
            public function handle(Request $r): Response
            {
                return new Response(200, ['Content-Type' => 'application/json'], '{"ok":1}');
            }
        };

        $g = new HmacGuard(new HmacVerifier('k'), $store);
        $g->process($req, $handler);
        $res2 = $g->process($req, $handler);
        $this->assertSame(401, $res2->status);
    }
}
