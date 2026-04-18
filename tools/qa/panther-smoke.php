<?php

declare(strict_types=1);

use Symfony\Component\Panther\Client;

require dirname(__DIR__, 2) . '/vendor/autoload.php';

$reservePort = static function (): int {
    $socket = @stream_socket_server('tcp://127.0.0.1:0', $errno, $errstr);
    if ($socket === false) {
        throw new RuntimeException(sprintf('Could not reserve a localhost port: %s', $errstr));
    }

    $address = stream_socket_get_name($socket, false);
    fclose($socket);

    return (int) substr((string) $address, strrpos((string) $address, ':') + 1);
};

$waitForServer = static function (int $port): void {
    $deadline = microtime(true) + 10;
    $url = sprintf('http://127.0.0.1:%d/healthz', $port);

    do {
        $body = @file_get_contents($url);
        if ($body !== false) {
            return;
        }

        usleep(100000);
    } while (microtime(true) < $deadline);

    throw new RuntimeException('Timed out while waiting for the Panther web server.');
};

$appPort = $reservePort();
$driverPort = $reservePort();
$command = sprintf('php -S 127.0.0.1:%d -t public public/index.php', $appPort);
$process = proc_open($command, [STDIN, STDOUT, STDERR], $pipes, dirname(__DIR__, 2));

if (!is_resource($process)) {
    throw new RuntimeException('Could not start the local PHP server for Panther.');
}

$client = null;

try {
    $waitForServer($appPort);

    $client = Client::createChromeClient(
        $_SERVER['PANTHER_CHROME_DRIVER_BINARY'] ?? 'drivers/chromedriver',
        null,
        ['port' => $driverPort],
        sprintf('http://127.0.0.1:%d', $appPort),
    );

    $crawler = $client->request('GET', '/healthz');
    $status = $client->getInternalResponse()?->getStatusCode();

    if ($status !== 200) {
        throw new RuntimeException(sprintf('Panther smoke failed with HTTP %s.', (string) $status));
    }

    if (!str_contains($crawler->filter('body')->text(), 'ok')) {
        throw new RuntimeException('Panther smoke did not find the expected response body.');
    }

    fwrite(STDOUT, "Panther smoke passed.\n");
} finally {
    if ($client instanceof Client) {
        $client->quit();
    }

    proc_terminate($process);
    proc_close($process);
}
