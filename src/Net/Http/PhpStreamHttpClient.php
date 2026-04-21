<?php

declare(strict_types=1);

namespace App\Rolling\Net\Http;

final class PhpStreamHttpClient implements SimpleHttpClientInterface
{
    /**
     * @param string      $method
     * @param string      $url
     * @param array       $headers
     * @param string|null $body
     * @param int         $timeoutMs
     *
     * @return array
     */
    public function request(string $method, string $url, array $headers = [], ?string $body = null, int $timeoutMs = 5000): array
    {
        $hdr = '';
        foreach ($headers as $k => $v) {
            $hdr .= $k.': '.$v."\r\n";
        }
        $ctx = stream_context_create(['http' => ['method' => $method, 'header' => $hdr, 'content' => $body ?? '', 'timeout' => $timeoutMs / 1000]]);
        $res = @file_get_contents($url, false, $ctx);
        $status = 200;
        if (isset($http_response_header) && is_array($http_response_header)) {
            foreach ($http_response_header as $line) {
                if (preg_match('#HTTP/\S+ (\d{3})#', $line, $m)) {
                    $status = (int) $m[1];
                    break;
                }
            }
        }

        return ['status' => $status, 'headers' => [], 'body' => (false === $res ? null : $res)];
    }
}
