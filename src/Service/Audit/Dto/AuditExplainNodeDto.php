<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\Service\Audit\Dto;

final class AuditExplainNodeDto
{
    /** @var AuditExplainNodeDto[] */
    public array $children = [];

    public function __construct(
        public string $type,
        public string $label,
        public bool $pass,
        public array $data = [],
    ) {
    }

    public function add(self $n): void
    {
        $this->children[] = $n;
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'label' => $this->label,
            'pass' => $this->pass,
            'data' => $this->data,
            'children' => array_map(fn ($c) => $c->toArray(), $this->children),
        ];
    }
}
