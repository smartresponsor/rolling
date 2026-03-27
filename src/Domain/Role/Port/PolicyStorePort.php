<?php

declare(strict_types=1);
/* Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp */

namespace App\Domain\Role\Port;

/**
 *
 */

/**
 *
 */
interface PolicyStorePort
{
    /**
     * @param string $tenant
     * @return string
     */
    public function getDraft(string $tenant): string;

    /**
     * @param string $tenant
     * @param string $expr
     * @return void
     */
    public function putDraft(string $tenant, string $expr): void;

    /**
     * @param string $tenant
     * @param string $note
     * @return string
     */
    public function publish(string $tenant, string $note = ''): string; // returns version id

    /**
     * @param string $tenant
     * @return string
     */
    public function getEffective(string $tenant): string;
}
