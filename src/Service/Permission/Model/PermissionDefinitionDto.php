<?php

declare(strict_types=1);

namespace App\Rolling\Service\Permission\Model;

final class PermissionDefinitionDto
{
    /**
     * @param list<string> $scopes
     */
    public function __construct(
        public string $key,
        public array $scopes = ['global'],
        public string $description = '',
        public ?string $component = null,
    ) {
    }

    /** @return array{key: string, scopes: list<string>, description: string, component: string|null} */
    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'scopes' => array_values($this->scopes),
            'description' => $this->description,
            'component' => $this->component,
        ];
    }
}
