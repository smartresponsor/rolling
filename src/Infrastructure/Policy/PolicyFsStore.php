<?php

declare(strict_types=1);
/* Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp */

namespace App\Rolling\Infrastructure\Policy;

use App\Rolling\ServiceInterface\PolicyStoreInterface;

final class PolicyFsStore implements PolicyStoreInterface
{
    public function __construct(private readonly string $dir)
    {
    }

    private function tenantDir(string $tenant): string
    {
        return rtrim($this->dir, '/').'/'.$tenant;
    }

    public function getDraft(string $tenant): string
    {
        $file = $this->tenantDir($tenant).'/draft.pel';

        return is_file($file) ? (string) file_get_contents($file) : '';
    }

    public function putDraft(string $tenant, string $expr): void
    {
        @mkdir($this->tenantDir($tenant), 0o775, true);
        file_put_contents($this->tenantDir($tenant).'/draft.pel', $expr);
    }

    public function publish(string $tenant, string $note = ''): string
    {
        @mkdir($this->tenantDir($tenant).'/versions', 0o775, true);

        $version = 'v'.date('YmdHis');
        $draft = $this->getDraft($tenant);

        file_put_contents($this->tenantDir($tenant).'/versions/'.$version.'.pel', $draft);
        file_put_contents($this->tenantDir($tenant).'/published.ver', $version);
        file_put_contents($this->tenantDir($tenant).'/notes_'.$version.'.txt', $note);

        return $version;
    }

    public function getEffective(string $tenant): string
    {
        $publishedFile = $this->tenantDir($tenant).'/published.ver';
        if (!is_file($publishedFile)) {
            return '';
        }

        $version = trim((string) file_get_contents($publishedFile));
        $versionFile = $this->tenantDir($tenant).'/versions/'.$version.'.pel';

        return is_file($versionFile) ? (string) file_get_contents($versionFile) : '';
    }
}
