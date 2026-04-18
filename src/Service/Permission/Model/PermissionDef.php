<?php

declare(strict_types=1);

<<<<<<< HEAD:src/Service/Permission/Model/PermissionDef.php
namespace App\Service\Permission\Model;
=======
namespace App\Permission\Role\Model;

>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa:src/Permission/Role/Model/PermissionDef.php
/**
 *
 */

/**
 *
 */
final class PermissionDef
{
    /**
     * @param string $key
     * @param array $scopes
     * @param string $description
     * @param string|null $component
     */
    public function __construct(public string $key, public array $scopes = ['global'], public string $description = '', public ?string $component = null) {}

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return ['key' => $this->key, 'scopes' => array_values($this->scopes), 'description' => $this->description, 'component' => $this->component];
    }
}
