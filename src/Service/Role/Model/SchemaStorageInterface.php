<?php

declare(strict_types=1);

namespace Model;

/**
 *
 */

/**
 *
 */
interface SchemaStorageInterface
{
    /** @return array{active:string|null, versions:array<string,string>} */
    public function load(): array;

    /** @param array{active:string|null, versions:array<string,string>} $state */
    public function save(array $state): void;
}
