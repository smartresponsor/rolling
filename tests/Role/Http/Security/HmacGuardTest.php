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

final class HmacGuardTest extends TestCase
{
    public function testOkPassesToHandler(): void
    {
        $request = $this->signedRequest();
        $guard = new HmacGuard(new HmacVerifier('k'), $this->replayStore());

        $response = $guard->process($request, $this->okHandler());

        $this->assertSame(200, $response->status);
    }

    public function testReplayBlocked(): void
    {
        $request = $this->signedRequest();
        $guard = new HmacGuard(new HmacVerifier('k'), $this->replayStore());

        $guard->process($request, $this->okHandler());
        $response = $guard->process($request, $this->okHandler());

        $this->assertSame(401, $response->status);
        $this->assertSame('replay', $response->headers['X-Auth-Error']);
    }

    public function testBypassesOtherPaths(): void
    {
        $request = $this->request('/healthz', [], '');
        $guard = new HmacGuard(new HmacVerifier('k'), $this->replayStore());

        $response = $guard->process($request, $this->okHandler());

        $this->assertSame(200, $response->status);
    }

    public function testInvalidSignatureReturnsStructuredUnauthorizedResponse(): void
    {
        $request = $this->request(
            '/v2/access/check',
            [
                'Date' => gmdate('D, d M Y H:i:s \G\M\T'),
                'X-Signature' => 'v1=bad',
            ],
            '{"a":1}',
        );
        $guard = new HmacGuard(new HmacVerifier('k'), $this->replayStore());

        $response = $guard->process($request, $this->okHandler());

        $this->assertSame(401, $response->status);
        $this->assertSame('signature_mismatch', $response->headers['X-Auth-Error']);
        $this->assertStringContainsString('"error":"unauthorized"', $response->body);
    }

    private function signedRequest(): Request
    {
        $date = gmdate('D, d M Y H:i:s \G\M\T');
        $body = '{"a":1}';
        $base = 'POST /v2/access/check' . "\n" . $date . "\n" . $body;
        $signature = 'v1=' . base64_encode(hash_hmac('sha256', $base, 'k', true));

        return $this->request('/v2/access/check', ['Date' => $date, 'X-Signature' => $signature], $body);
    }

    private function request(string $path, array $headers, string $body): Request
    {
        return new class ($path, $headers, $body) implements Request {
            public function __construct(
                private readonly string $path,
                private readonly array $headers,
                private readonly string $body,
            ) {}

            public function method(): string
            {
                return 'POST';
            }

            public function path(): string
            {
                return $this->path;
            }

            public function header(string $name): ?string
            {
                return $this->headers[$name] ?? null;
            }

            public function body(): string
            {
                return $this->body;
            }
        };
    }

    private function replayStore(): StoreInterface
    {
        return new class implements StoreInterface {
            private array $seen = [];

            public function seen(string $nonce, int $ttl): bool
            {
                if (isset($this->seen[$nonce])) {
                    return false;
                }

                $this->seen[$nonce] = time() + $ttl;

                return true;
            }
        };
    }

    private function okHandler(): HandlerInterface
    {
        return new class implements HandlerInterface {
            public function handle(Request $request): Response
            {
                return new Response(200, ['Content-Type' => 'application/json'], '{"ok":1}');
            }
        };
    }
}
