<?php

declare(strict_types=1);

namespace App\Rolling\DTO\Http\Role\Admin;

final readonly class AdminApprovalDecisionPayload
{
    public function __construct(
        public string $id,
        public string $subject,
        public string $comment,
        public string $reason,
        public string $actor,
    ) {
    }

    /**
     * @param array<string,mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self(
            id: (string) ($payload['id'] ?? ''),
            subject: (string) ($payload['subject'] ?? ''),
            comment: (string) ($payload['comment'] ?? ''),
            reason: (string) ($payload['reason'] ?? ''),
            actor: (string) ($payload['actor'] ?? ''),
        );
    }
}
