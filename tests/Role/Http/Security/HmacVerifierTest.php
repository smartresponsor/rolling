<?php
declare(strict_types=1);

namespace Tests\Role\Http\Security;

use Http\Security\HmacVerifier;
use PHPUnit\Framework\TestCase;

/**
 *
 */

/**
 *
 */
final class HmacVerifierTest extends TestCase
{
    /**
     * @return void
     */
    public function testVerifyOk(): void
    {
        $v = new HmacVerifier('secret', 300);
        $date = gmdate('D, d M Y H:i:s \G\M\T');
        $method = 'POST';
        $path = '/v2/access/check';
        $body = '{"a":1}';
        $base = $method . ' ' . $path . "\n" . $date . "\n" . $body;
        $sig = 'v1=' . base64_encode(hash_hmac('sha256', $base, 'secret', true));

        $res = $v->verify($method, $path, $date, $body, $sig);
        $this->assertTrue($res['ok']);
    }

    /**
     * @return void
     */
    public function testVerifySkewFails(): void
    {
        $v = new HmacVerifier('secret', 1);
        $date = gmdate('D, d M Y H:i:s \G\M\T', time() - 3600);
        $res = $v->verify('POST', '/v2/access/check', $date, '{}', 'v1=aaa');
        $this->assertFalse($res['ok']);
    }
}
