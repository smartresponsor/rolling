<?php
declare(strict_types=1);

namespace Tests\SDK\PHP;

require_once __DIR__ . '/psr_stubs.php';

use PHPUnit\Framework\TestCase;
use SmartResponsor\RoleSdk\V2\Client;
use SmartResponsor\RoleSdk\V2\Types;
use SmartResponsor\RoleSdk\V2\Exceptions;
use Tests\Support\{DummyHttpClient, MemoryRequestFactory, MemoryStreamFactory, MemoryResponse};

/**
 *
 */

/**
 *
 */
final class ClientContractTest extends TestCase
{
    /**
     * @return void
     */
    public function testCheckSuccessAndHmac(): void
    {
        $fixedTs = 1_700_000_000;
        $clock = fn(): int => $fixedTs;

        $responder = function () {
            $body = json_encode(['decision' => 'ALLOW', 'reason' => 'ok', 'obligations' => [], 'scope' => 'tenant:t1']);
            return new MemoryResponse(200, ['Content-Type' => ['application/json']], $body);
        };
        $http = new DummyHttpClient($responder);

        $cli = new Client('https://pdp.example', $http, new MemoryRequestFactory(), new MemoryStreamFactory(), apiKey: 'k1', hmacSecret: 's1', clock: $clock);
        $req = Types::accessCheck('u1', 'message.read', 'tenant', 't1');
        $resp = $cli->check($req);

        $this->assertSame('ALLOW', $resp['decision']);
        // Verify signature header exists and has expected prefix
        $sig = $http->last->getHeaderLine('X-Signature');
        $this->assertStringStartsWith('v1=', $sig);
        // Verify Authorization
        $this->assertSame('Bearer k1', $http->last->getHeaderLine('Authorization'));

        // Check Date header deterministic
        $this->assertSame(gmdate('D, d M Y H:i:s \G\M\T', $fixedTs), $http->last->getHeaderLine('Date'));
    }

    /**
     * @return void
     */
    public function testErrorBecomesException(): void
    {
        $http = new DummyHttpClient(fn($req) => new MemoryResponse(401, ['Content-Type' => ['application/json']], '{"error":"unauthorized"}'));
        $cli = new Client('https://pdp.example', $http, new MemoryRequestFactory(), new MemoryStreamFactory());
        $this->expectException(Exceptions::class);
        $cli->check(Types::accessCheck('u', 'a', 'global'));
    }
}
