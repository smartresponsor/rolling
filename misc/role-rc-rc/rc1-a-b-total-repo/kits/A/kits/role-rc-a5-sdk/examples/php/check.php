<?php
declare(strict_types=1);
require __DIR__ . '/../../vendor/autoload.php';

use SmartResponsor\RoleSdk\V2\Client;
use SmartResponsor\RoleSdk\V2\Types;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\HttpFactory;

$base = getenv('ROLE_PDP_BASE_URL') ?: 'http://localhost:8000';
$apiKey = getenv('ROLE_PDP_API_KEY') ?: null;
$hmac = getenv('ROLE_PDP_HMAC') ?: null;

$http = new GuzzleClient();
$factory = new HttpFactory();

$sdk = new Client($base, $http, $factory, $factory, apiKey: $apiKey, hmacSecret: $hmac);
$req = Types::accessCheck('u1', 'message.read', 'global');
$res = $sdk->check($req);

echo json_encode($res, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), PHP_EOL;
