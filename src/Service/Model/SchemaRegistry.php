<?php

declare(strict_types=1);

namespace App\Rolling\Service\Model;

use App\Rolling\ServiceInterface\Model\SchemaStorageInterface;

final class SchemaRegistry
{
    public function __construct(private readonly SchemaStorageInterface $storage)
    {
    }

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

        return json_decode($all['versions'][$version], true);
    }

    /** @return array{ok:bool, errors:list<string>} */
    public function create(string $version, array $schema): array
    {
        $errors = Validation::validate($schema);
        if ([] !== $errors) {
            return ['ok' => false, 'errors' => $errors];
        }

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

        $all['active'] = $version;
        $this->storage->save($all);

        return true;
    }
}
