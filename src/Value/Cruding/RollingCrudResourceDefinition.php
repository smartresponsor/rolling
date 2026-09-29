<?php

declare(strict_types=1);

namespace App\Rolling\Value\Cruding;

/**
 * Rolling-owned resource metadata for the generic Cruding integration.
 *
 * The metadata remains independent from the native EasyAdmin administrative
 * surface. Canon021 permits that back-office surface while Cruding continues
 * to own generic application CRUD routing and processing.
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
