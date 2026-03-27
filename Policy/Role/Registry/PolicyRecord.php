<?php

declare(strict_types=1);

namespace Policy\Role\Registry;

/**
 *
 */

/**
 *
 */
final class PolicyRecord
{
    /**
     * @param string $ns
     * @param string $name
     * @param string $version
     * @param string $docJson
     * @param int $createdAt
     * @param bool $isActive
     */
    public function __construct(
        public string $ns,
        public string $name,
        public string $version,
        public string $docJson,
        public int    $createdAt,
        public bool   $isActive,
    ) {}
}
