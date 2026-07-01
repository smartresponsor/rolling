<?php

declare(strict_types=1);

namespace App\Rolling\Value\Cruding;

/**
 * Rolling-side resource metadata prepared for the Cruding migration.
 *
 * This value object intentionally does not depend on Cruding classes yet. The
 * later Cruding adapter may translate it into CrudResourceContract/resource
 * provider registrations once the composer dependency and lock file are in
 * place.
 */
final readonly class RollingCrudResourceDefinition
{
    /**
     * @param list<string>               $operations
     * @param list<array<string, mixed>> $fields
     * @param array<string, mixed>       $metadata
     */
    public function __construct(
        public string $resourceKey,
        public string $entityClass,
        public string $label,
        public array $operations,
        public array $fields,
        public array $metadata = [],
    ) {
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'resource_key' => $this->resourceKey,
            'entity_class' => $this->entityClass,
            'label' => $this->label,
            'operations' => $this->operations,
            'fields' => $this->fields,
            'metadata' => $this->metadata,
        ];
    }
}
