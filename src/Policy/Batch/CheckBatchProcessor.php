<?php

declare(strict_types=1);

namespace App\Policy\Batch;

use App\Entity\Role\PermissionKey;
use App\Entity\Role\Scope;
use App\Entity\Role\SubjectId;
use App\ServiceInterface\Policy\PdpV2Interface;

final class CheckBatchProcessor
{
    private const int DEFAULT_CHUNK_SIZE = 100;
    private const int DEFAULT_MAX_ITEMS = 1000;

    /**
     * @param PdpV2Interface $pdp
     */
    public function __construct(private readonly PdpV2Interface $pdp)
    {
    }

    /**
     * @param iterable<array<string,mixed>>                                          $requests
     * @param array{chunkSize?:int,maxItems?:int,onProgress?:callable(int,int):void} $opts
     *
     * @return \Generator<int,array<string,mixed>>
     */
    public function process(iterable $requests, array $opts = []): \Generator
    {
        $chunkSize = $this->normalizePositiveInt($opts['chunkSize'] ?? null, self::DEFAULT_CHUNK_SIZE);
        $maxItems = $this->normalizePositiveInt($opts['maxItems'] ?? null, self::DEFAULT_MAX_ITEMS);
        /** @var callable(int,int):void|null $progress */
        $progress = $opts['onProgress'] ?? null;

        $buffer = [];
        $index = 0;
        foreach ($requests as $request) {
            if ($index >= $maxItems) {
                break;
            }

            $buffer[] = [$index, $this->normalize($request)];
            if (count($buffer) >= $chunkSize) {
                yield from $this->handleChunk($buffer, $progress);
                $buffer = [];
            }

            ++$index;
        }

        if ([] !== $buffer) {
            yield from $this->handleChunk($buffer, $progress);
        }
    }

    /**
     * @param list<array{0:int,1:array<string,mixed>}> $buffer
     * @param callable(int,int):void|null              $progress
     *
     * @return \Generator<int,array<string,mixed>>
     */
    private function handleChunk(array $buffer, ?callable $progress): \Generator
    {
        $done = 0;
        $total = count($buffer);

        foreach ($buffer as [$index, $request]) {
            try {
                yield $this->processOne($index, $request);
            } catch (\Throwable $e) {
                yield $this->failureResult($index, $e);
            } finally {
                ++$done;
                if ($progress) {
                    $progress($done, $total);
                }
            }
        }
    }

    /**
     * @param array<string,mixed> $request
     *
     * @return array<string,mixed>
     */
    private function normalize(array $request): array
    {
        $request['scopeType'] = $request['scopeType'] ?? 'global';
        $request['context'] = (array) ($request['context'] ?? []);

        return $request;
    }

    /**
     * @param array<string,mixed> $request
     *
     * @return array<string,mixed>
     */
    private function processOne(int $index, array $request): array
    {
        $subject = new SubjectId((string) ($request['subjectId'] ?? ''));
        $action = new PermissionKey((string) ($request['action'] ?? ''));
        $scope = $this->buildScope($request);
        /** @var array<string,mixed> $context */
        $context = $request['context'];
        $decision = $this->pdp->check($subject, $action, $scope, $context);

        return [
            'idx' => $index,
            'ok' => true,
            'decision' => $decision->isAllow() ? 'ALLOW' : 'DENY',
            'reason' => $decision->reason(),
            'scope' => $scope->key(),
        ];
    }

    /**
     * @param array<string,mixed> $request
     */
    private function buildScope(array $request): Scope
    {
        return match ($request['scopeType']) {
            'tenant' => Scope::tenant((string) ($request['tenantId'] ?? '')),
            'resource' => Scope::resource((string) ($request['tenantId'] ?? ''), (string) ($request['resourceId'] ?? '')),
            default => Scope::global(),
        };
    }

    /**
     * @return array<string,mixed>
     */
    private function failureResult(int $index, \Throwable $exception): array
    {
        return [
            'idx' => $index,
            'ok' => false,
            'error' => $exception::class,
            'message' => $exception->getMessage(),
        ];
    }

    private function normalizePositiveInt(mixed $value, int $default): int
    {
        $normalized = (int) $value;

        return $normalized > 0 ? $normalized : $default;
    }
}
