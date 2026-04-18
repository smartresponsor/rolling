<?php
declare(strict_types=1);

namespace Rolling\SDK\V2;

use InvalidArgumentException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\ResponseInterface;

/**
 *
 */

/**
 *
 */
final class Client
{
    /** @var callable():int */
    private $clock;

    /**
     * @param string $baseUrl
     * @param \Psr\Http\Client\ClientInterface $http
     * @param \Psr\Http\Message\RequestFactoryInterface $reqFactory
     * @param \Psr\Http\Message\StreamFactoryInterface $streamFactory
     * @param string|null $apiKey
     * @param string|null $hmacSecret
     * @param callable|null $clock
     */
    public function __construct(
        private string                           $baseUrl,
        private readonly ClientInterface         $http,
        private readonly RequestFactoryInterface $reqFactory,
        private readonly StreamFactoryInterface  $streamFactory,
        private readonly ?string                 $apiKey = null,
        private readonly ?string                 $hmacSecret = null,
        ?callable                                $clock = null
    )
    {
        $this->clock = $clock ?? static fn(): int => time();
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    /**
     * @param array $req @return array<string,mixed>
     * @return array
     */
    public function check(array $req): array
    {
        return $this->postJson('/v2/access/check', $req);
    }

    /**
     * @param array $requests @return array<string,mixed>
     * @return array
     */
    public function checkBatch(array $requests): array
    {
        return $this->postJson('/v2/access/check:batch', ['requests' => $requests]);
    }

    /**
     * @param string $path
     * @param array $payload @return array<string,mixed>
     * @return array
     */
    private function postJson(string $path, array $payload): array
    {
        $url = $this->baseUrl . $path;
        $body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($body === false) {
            throw new InvalidArgumentException('Invalid JSON payload');
        }

        $req = $this->reqFactory->createRequest('POST', $url)
            ->withHeader('Content-Type', 'application/json');

        if ($this->apiKey) {
            $req = $req->withHeader('Authorization', 'Bearer ' . $this->apiKey);
        }

        $date = gmdate('D, d M Y H:i:s \G\M\T', ($this->clock)());
        $req = $req->withHeader('Date', $date);

        if ($this->hmacSecret) {
            $sig = $this->computeSignature($path, $date, $body, $this->hmacSecret);
            $req = $req->withHeader('X-Signature', $sig);
        }

        $req = $req->withBody($this->streamFactory->createStream($body));
        $resp = $this->http->sendRequest($req);
        $code = $resp->getStatusCode();
        $respBody = (string)$resp->getBody();
        if ($code >= 200 && $code < 300) {
            $data = json_decode($respBody, true);
            if (!is_array($data)) {
                throw new Exceptions($code, $this->headers($resp), $respBody, 'Invalid JSON response');
            }
            return $data;
        }
        throw new Exceptions($code, $this->headers($resp), $respBody);
    }

    /**
     * @param string $path
     * @param string $date
     * @param string $body
     * @param string $secret
     * @return string
     */
    private function computeSignature(string $path, string $date, string $body, string $secret): string
    {
        $base = strtoupper('POST') . ' ' . $path . "\n" . $date . "\n" . $body;
        $mac = base64_encode(hash_hmac('sha256', $base, $secret, true));
        return 'v1=' . $mac;
    }

    /** @return array<string,string> */
    private function headers(ResponseInterface $r): array
    {
        $out = [];
        foreach ($r->getHeaders() as $k => $vals) {
            $out[$k] = implode(', ', $vals);
        }
        return $out;
    }
}
