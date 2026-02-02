<?php
declare(strict_types=1);

namespace Http\Role\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 *
 */

/**
 *
 */
final class WatchController
{
    private string $path;

    /**
     * @param string $streamPath
     */
    public function __construct(string $streamPath = __DIR__ . '/../../../../var/tuples.ndjson')
    {
        $this->path = $streamPath;
        if (!is_dir(dirname($this->path))) {
            @mkdir(dirname($this->path), 0775, true);
        }
        if (!file_exists($this->path)) {
            touch($this->path);
        }
    }

    /** Server-Sent Events: streams tuple changes as ndjson wrapped in SSE 'data:' lines */
    public function watch(Request $req): StreamedResponse
    {
        $since = (int)($req->query->get('offset') ?? 0);
        $resp = new StreamedResponse(function () use ($since) {
            @ob_end_flush();
            @ob_implicit_flush(1);
            $f = fopen($this->path, 'r');
            if ($f === false) {
                echo ": cannot open stream\n\n";
                return;
            }
            $pos = $since;
            fseek($f, $pos);
            while (!connection_aborted()) {
                $line = fgets($f);
                if ($line === false) {
                    clearstatcache();
                    usleep(200 * 1000); // 200ms backoff
                    continue;
                }
                $pos = ftell($f);
                $payload = trim($line);
                if ($payload === '') continue;
                echo "event: tuple\n";
                echo "data: " . $payload . "\n";
                echo "id: " . $pos . "\n\n";
                flush();
            }
            fclose($f);
        });
        $resp->headers->set('Content-Type', 'text/event-stream');
        $resp->headers->set('Cache-Control', 'no-cache');
        $resp->headers->set('X-Accel-Buffering', 'no'); // for nginx
        return $resp;
    }
}
