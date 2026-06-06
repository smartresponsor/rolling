<?php

declare(strict_types=1);

namespace App\Rolling\DTO\Http\Role\Admin;

final readonly class AdminDelegationPayload
{
    public function __construct(
        public string $tenant,
        public string $from,
        public string $to,
        public int $until,
        public string $scope,
    ) {
    }

    /**
     * @param array<string,mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self(
            tenant: (string) ($payload['tenant'] ?? 't1'),
            from: (string) ($payload['from'] ?? ''),
            to: (string) ($payload['to'] ?? ''),
            until: (int) ($payload['until'] ?? (time() + 3600)),
            scope: (string) ($payload['scope'] ?? '*'),
        );
    }

    /**
     * @return array{from:string,to:string,until:int,scope:string}
     */
    public function toRow(): array
    {
        return [
            'from' => $this->from,
            'to' => $this->to,
            'until' => $this->until,
            'scope' => $this->scope,
        ];
    }
}
