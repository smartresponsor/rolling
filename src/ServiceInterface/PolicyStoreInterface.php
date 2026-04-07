<?php
declare(strict_types=1);
/* Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp */

namespace App\ServiceInterface;

interface PolicyStoreInterface
{
    public function getDraft(string $tenant): string;

    public function putDraft(string $tenant, string $expr): void;

    /** @return string version id */
    public function publish(string $tenant, string $note = ''): string;

    public function getEffective(string $tenant): string;
}
