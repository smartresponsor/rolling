<?php

declare(strict_types=1);

namespace Tests\Panther;

use RuntimeException;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use Symfony\Component\Panther\PantherTestCase;

final class HealthPantherTest extends PantherTestCase
{
    #[RunInSeparateProcess]
    public function testHealthEndpointResponds(): void
    {
        $port = $this->reservePort();
        $driverPort = $this->reservePort();
        $command = sprintf('php -S 127.0.0.1:%d -t public public/index.php', $port);
        $process = proc_open($command, [STDIN, STDOUT, STDERR], $pipes, dirname(__DIR__, 2));

        if (!is_resource($process)) {
            throw new RuntimeException('Could not start the local PHP server for Panther.');
        }

        try {
            $this->waitForServer($port);

            $client = static::createPantherClient([
                'browser' => static::CHROME,
                'external_base_uri' => sprintf('http://127.0.0.1:%d', $port),
            ], [], ['port' => $driverPort]);

            $crawler = $client->request('GET', '/healthz');

            self::assertSame(200, $client->getInternalResponse()->getStatusCode());
            self::assertStringContainsString('ok', $crawler->filter('body')->text());
        } finally {
            proc_terminate($process);
            proc_close($process);
        }
    }

    private function waitForServer(int $port): void
    {
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
    }

    private function reservePort(): int
    {
        $socket = @stream_socket_server('tcp://127.0.0.1:0', $errno, $errstr);
        if ($socket === false) {
            throw new RuntimeException(sprintf('Could not reserve a localhost port: %s', $errstr));
        }

        $address = stream_socket_get_name($socket, false);
        fclose($socket);

        return (int) substr((string) $address, strrpos((string) $address, ':') + 1);
    }
}
