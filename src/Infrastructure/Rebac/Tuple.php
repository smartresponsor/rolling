<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Infrastructure\Rebac;

/**
 *
 */

/**
 *
 */
final class Tuple
{
    /**
     * @param string $userType
     * @param string $userId
     * @param string $relation
     * @param string $objectType
     * @param string $objectId
     * @param string|null $tenant
     */
    public function __construct(
        public string  $userType,
        public string  $userId,
        public string  $relation,
        public string  $objectType,
        public string  $objectId,
        public ?string $tenant = null,
    ) {}

    /**
     * @param array $a
     * @return self
     */
    public static function fromArray(array $a): self
    {
        return new self(
            $a['userType'],
            $a['userId'],
            $a['relation'],
            $a['objectType'],
            $a['objectId'],
            $a['tenant'] ?? null,
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'userType' => $this->userType,
            'userId' => $this->userId,
            'relation' => $this->relation,
            'objectType' => $this->objectType,
            'objectId' => $this->objectId,
            'tenant' => $this->tenant,
        ];
    }
}
