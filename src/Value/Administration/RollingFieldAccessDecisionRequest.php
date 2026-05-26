<?php

declare(strict_types=1);

namespace App\Rolling\Value\Administration;

/**
 * Field-level access request shape accepted by future Rolling decision services.
 */
final readonly class RollingFieldAccessDecisionRequest
{
    /** @param array<string, mixed> $attributes */
    public function __construct(
        public string $permissionKey,
        public string $componentKey,
        public string $resourceClass,
        public string $fieldName,
        public string $pageName,
        public string $operation = 'view',
        public ?string $subjectIdentifier = null,
        public array $attributes = [],
    ) {
    }

    /** @return array<string, mixed> */
    public function toAttributes(): array
    {
        return [
            'permission' => $this->permissionKey,
            'component' => $this->componentKey,
            'resource' => $this->resourceClass,
            'field' => $this->fieldName,
            'page' => $this->pageName,
            'operation' => $this->operation,
            'subject' => $this->subjectIdentifier,
        ] + $this->attributes;
    }
}
