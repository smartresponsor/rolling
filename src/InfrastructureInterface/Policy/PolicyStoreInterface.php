<?php

declare(strict_types=1);
/* Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp */

namespace App\Rolling\InfrastructureInterface\Policy;

interface PolicyStoreInterface
{
    public function getDraft(string $tenant): string;

    public function putDraft(string $tenant, string $expr): void;

    public function publish(string $tenant, string $note = ''): string; // returns version id

    public function getEffective(string $tenant): string;
}
