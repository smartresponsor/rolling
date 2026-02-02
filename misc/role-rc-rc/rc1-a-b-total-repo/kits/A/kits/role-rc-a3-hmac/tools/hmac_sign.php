<?php
// php tools/hmac_sign.php POST /v2/access/check '{"foo":"bar"}' supersecret 'RFC1123|now'
[$script, $method, $path, $body, $secret, $dateArg] = $argv + [null, null, null, null, null, null];
if (!$method || !$path || $body === null || !$secret) {
    fwrite(STDERR, "Usage: php tools/hmac_sign.php METHOD PATH BODY_JSON SECRET [RFC1123|now]\n");
    exit(2);
}
$date = ($dateArg && $dateArg !== 'now') ? $dateArg : gmdate('D, d M Y H:i:s \G\M\T');
$base = strtoupper($method) . ' ' . $path . "\n" . $date . "\n" . $body;
$sig = 'v1=' . base64_encode(hash_hmac('sha256', $base, $secret, true));
echo json_encode(['date' => $date, 'signature' => $sig], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;
