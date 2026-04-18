<?php

declare(strict_types=1);

namespace App\Service\Model;

use App\ServiceInterface\Model\SchemaStorageInterface;

final class SchemaRegistry
{
<<<<<<< HEAD:src/Service/Model/SchemaRegistry.php
    public function __construct(private readonly SchemaStorageInterface $storage)
    {
    }
=======
    /** @param SchemaStorageInterface $storage */
    public function __construct(private readonly SchemaStorageInterface $storage) {}
>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa:src/Service/Role/Model/SchemaRegistry.php

    /** @return array<string,string> */
    public function versions(): array
    {
        return $this->storage->load()['versions'];
    }

    public function active(): ?string
    {
        return $this->storage->load()['active'];
    }

    public function get(string $version): ?array
    {
        $all = $this->storage->load();
        if (!isset($all['versions'][$version])) {
            return null;
        }
<<<<<<< HEAD:src/Service/Model/SchemaRegistry.php

=======
>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa:src/Service/Role/Model/SchemaRegistry.php
        return json_decode($all['versions'][$version], true);
    }

    /** @return array{ok:bool, errors:list<string>} */
    public function create(string $version, array $schema): array
    {
        $errors = Validation::validate($schema);
<<<<<<< HEAD:src/Service/Model/SchemaRegistry.php
        if ($errors !== []) {
            return ['ok' => false, 'errors' => $errors];
        }

=======
        if ($errors) {
            return ['ok' => false, 'errors' => $errors];
        }
>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa:src/Service/Role/Model/SchemaRegistry.php
        $all = $this->storage->load();
        if (isset($all['versions'][$version])) {
            return ['ok' => false, 'errors' => ['version already exists']];
        }

        $all['versions'][$version] = json_encode($schema, JSON_UNESCAPED_SLASHES);
        $this->storage->save($all);

        return ['ok' => true, 'errors' => []];
    }

    public function activate(string $version): bool
    {
        $all = $this->storage->load();
        if (!isset($all['versions'][$version])) {
            return false;
        }
<<<<<<< HEAD:src/Service/Model/SchemaRegistry.php

=======
>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa:src/Service/Role/Model/SchemaRegistry.php
        $all['active'] = $version;
        $this->storage->save($all);

        return true;
    }
}
