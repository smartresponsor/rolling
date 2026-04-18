<?php

declare(strict_types=1);

namespace App\Service\Model;

/**
 *
 */

/**
 *
 */
use App\ServiceInterface\Model\SchemaStorageInterface;

final class FileSchemaStorage implements SchemaStorageInterface
{
    /**
     * @param string $path
     */
    public function __construct(private readonly string $path)
    {
        if (!is_dir(dirname($this->path))) {
            @mkdir(dirname($this->path), 0775, true);
        }
        if (!file_exists($this->path)) {
            file_put_contents($this->path, json_encode(['active' => null, 'versions' => []], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
    }

    /**
     * @return array
     */
    public function load(): array
    {
        $raw = (string) @file_get_contents($this->path);
        $data = json_decode($raw, true);
        if (!is_array($data)) {
            $data = ['active' => null, 'versions' => []];
        }
        $data['versions'] = $data['versions'] ?? [];
        return $data;
    }

    /**
     * @param array $state
     * @return void
     */
    public function save(array $state): void
    {
        file_put_contents($this->path, json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}
