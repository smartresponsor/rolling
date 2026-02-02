<?php
declare(strict_types=1);

namespace Policy\Role\Batch;

use Generator;
use PolicyInterface\Role\PdpV2Interface;
use src\Entity\Role\{Scope};
use src\Entity\Role\PermissionKey;
use src\Entity\Role\SubjectId;
use Throwable;

/**
 *
 */

/**
 *
 */
final class CheckBatchProcessor
{
    /**
     * @param \PolicyInterface\Role\PdpV2Interface $pdp
     */
    public function __construct(private readonly PdpV2Interface $pdp)
    {
    }

    /**
     * @param iterable<array<string,mixed>> $requests
     * @param array{chunkSize?:int,maxItems?:int,onProgress?:callable(int,int):void} $opts
     * @return \Generator<int,array<string,mixed>>
     */
    public function process(iterable $requests, array $opts = []): Generator
    {
        $chunk = (int)($opts['chunkSize'] ?? 128);
        $limit = (int)($opts['maxItems'] ?? 10000);
        /** @var callable(int,int):void|null $progress */
        $progress = $opts['onProgress'] ?? null;

        $buf = [];
        $i = 0;
        foreach ($requests as $req) {
            if ($i >= $limit) break;
            $buf[] = [$i, $this->normalize($req)];
            if (count($buf) >= $chunk) {
                yield from $this->handleChunk($buf, $progress);
                $buf = [];
            }
            $i++;
        }
        if ($buf) {
            yield from $this->handleChunk($buf, $progress);
        }
    }

    /**
     * @param array $buf
     * @param callable|null $progress
     * @return \Generator<int,array<string,mixed>>
     */
    private function handleChunk(array $buf, ?callable $progress): Generator
    {
        $done = 0;
        $total = count($buf);
        foreach ($buf as [$idx, $r]) {
            try {
                $sid = new SubjectId((string)($r['subjectId'] ?? ''));
                $act = new PermissionKey((string)($r['action'] ?? ''));
                $sc = match ($r['scopeType'] ?? 'global') {
                    'tenant' => Scope::tenant((string)($r['tenantId'] ?? '')),
                    'resource' => Scope::resource((string)($r['tenantId'] ?? ''), (string)($r['resourceId'] ?? '')),
                    default => Scope::global()
                };
                /** @var array<string,mixed> $ctx */
                $ctx = (array)($r['context'] ?? []);
                $dec = $this->pdp->check($sid, $act, $sc, $ctx);
                yield [
                    'idx' => $idx,
                    'ok' => true,
                    'decision' => $dec->isAllow() ? 'ALLOW' : 'DENY',
                    'reason' => $dec->reason,
                    'scope' => $sc->key(),
                ];
            } catch (Throwable $e) {
                yield [
                    'idx' => $idx,
                    'ok' => false,
                    'error' => get_class($e),
                    'message' => $e->getMessage(),
                ];
            } finally {
                $done++;
                if ($progress) {
                    $progress($done, $total);
                }
            }
        }
    }

    /**
     * @param array $r
     * @return array
     */
    private function normalize(array $r): array
    {
        // лёгкая нормализация: гарантируем ключи
        $r['scopeType'] = $r['scopeType'] ?? 'global';
        $r['context'] = (array)($r['context'] ?? []);
        return $r;
    }
}
