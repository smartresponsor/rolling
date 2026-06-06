<?php

declare(strict_types=1);

namespace App\Rolling\DTO\Http\Role;

final readonly class RebacWritePayload
{
    /**
     * @param list<array<string,mixed>> $tuples
     */
    public function __construct(
        public string $namespace,
        public array $tuples,
    ) {
    }

    /**
     * @param array<string,mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        $tuples = [];
        foreach ((array) ($payload['tuples'] ?? []) as $tuple) {
            if (is_array($tuple)) {
                /** @var array<string,mixed> $tuple */
                $tuples[] = $tuple;
            }
        }

        return new self(
            namespace: (string) ($payload['ns'] ?? 'default'),
            tuples: $tuples,
        );
    }
}
