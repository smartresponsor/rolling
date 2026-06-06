<?php

declare(strict_types=1);

namespace App\Rolling\DTO\Http\Role\Pipeline;

final readonly class PipelineEvalBatchPayload
{
    /**
     * @param list<PipelineEvalPayload> $items
     */
    public function __construct(public array $items)
    {
    }

    /**
     * @param array<string,mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        $items = [];
        foreach ((array) ($payload['list'] ?? []) as $row) {
            if (is_array($row)) {
                /** @var array<string,mixed> $row */
                $items[] = PipelineEvalPayload::fromArray($row);
            }
        }

        return new self($items);
    }
}
