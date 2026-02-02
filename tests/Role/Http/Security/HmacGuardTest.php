<?php
declare(strict_types=1);

namespace Tests\Role\Http\Security;

use Http\Middleware\Security\HmacGuard;
use Http\Response;
use Http\Security\HmacVerifier;
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
final private class DummyStore implements StoreInterface
{
    private array $seen = [];

    /**
     * @param string $n
     * @param int $ttl
     * @return bool
     */
    public function seen(string $n, int $ttl): bool
    {
        if (isset($this->seen[$n])) return false;
        $this->seen[$n] = time() + $ttl;
        return true;
    }
}

private

/**
 *
 */

/**
 *
 */
final class DummyRequest implements Request
{
    /**
     * @param array $h
     * @param string $body
     */
    public function __construct(private readonly array $h, private readonly string $body)
    {
    }

    /**
     * @return string
     */
    public function method(): string
    {
        return 'POST';
    }

    /**
     * @return string
     */
    public function path(): string
    {
        return '/v2/access/check';
    }

    /**
     * @param string $n
     * @return string|null
     */
    public function header(string $n): ?string
    {
        return $this->h[$n] ?? null;
    }

    /**
     * @return string
     */
    public function body(): string
    {
        return $this->body;
    }
}

private

/**
 *
 */

/**
 *
 */
final class DummyHandler implements HandlerInterface
{
    /**
     * @param \Http\Server\Request $r
     * @return \Http\Response
     */
    public function handle(Request $r): Response
    {
        return new Response(200, ['Content-Type' => 'application/json'], '{"ok":1}');
    }
}

public
/**
 * @return void
 */
function testOkPassesToHandler(): void
{
    $date = gmdate('D, d M Y H:i:s \G\M\T');
    $body = '{"a":1}';
    $base = 'POST /v2/access/check' . "\n" . $date . "\n" . $body;
    $sig = 'v1=' . base64_encode(hash_hmac('sha256', $base, 'k', true));
    $req = new self::DummyRequest(['Date' => $date, 'X-Signature' => $sig], $body);

    $g = new HmacGuard(new HmacVerifier('k'), new self::DummyStore());
    $res = $g->process($req, new self::DummyHandler());
    $this->assertSame(200, $res->status);
}

public
/**
 * @return void
 */
function testReplayBlocked(): void
{
    $date = gmdate('D, d M Y H:i:s \G\M\T');
    $body = '{"a":1}';
    $base = 'POST /v2/access/check' . "\n" . $date . "\n" . $body;
    $sig = 'v1=' . base64_encode(hash_hmac('sha256', $base, 'k', true));
    $req = new self::DummyRequest(['Date' => $date, 'X-Signature' => $sig], $body);

    $g = new HmacGuard(new HmacVerifier('k'), new self::DummyStore());
    $g->process($req, new self::DummyHandler());
    $res2 = $g->process($req, new self::DummyHandler());
    $this->assertSame(401, $res2->status);
}
}
