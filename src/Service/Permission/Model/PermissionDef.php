<?php

declare(strict_types=1);

namespace App\Rolling\Service\Permission\Model;

final class PermissionDef
{
    /**
     * @param string      $key
     * @param array       $scopes
     * @param string      $description
     * @param string|null $component
     */
    public function __construct(public string $key, public array $scopes = ['global'], public string $description = '', public ?string $component = null)
    {
    }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return ['key' => $this->key, 'scopes' => array_values($this->scopes), 'description' => $this->description, 'component' => $this->component];
    }
}
