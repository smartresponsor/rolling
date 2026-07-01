<?php

declare(strict_types=1);
/* Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp */

namespace App\Rolling\Infrastructure\Role\Policy;

use App\Rolling\InfrastructureInterface\Policy\PolicyStoreInterface;

final class PolicyFsStore implements PolicyStoreInterface
{
    public function __construct(private readonly string $dir)
    {
    }

    private function tdir(string $tenant): string
    {
        return rtrim($this->dir, '/')."/$tenant";
    }

    public function getDraft(string $tenant): string
    {
        $f = $this->tdir($tenant).'/draft.pel';

        return is_file($f) ? (string) file_get_contents($f) : '';
    }

    public function putDraft(string $tenant, string $expr): void
    {
        @mkdir($this->tdir($tenant), 0o775, true);
        file_put_contents($this->tdir($tenant).'/draft.pel', $expr);
    }

    public function publish(string $tenant, string $note = ''): string
    {
        @mkdir($this->tdir($tenant).'/versions', 0o775, true);
        $ver = 'v'.date('YmdHis');
        $draft = $this->getDraft($tenant);
        file_put_contents($this->tdir($tenant)."/versions/$ver.pel", $draft);
        file_put_contents($this->tdir($tenant).'/published.ver', $ver);
        file_put_contents($this->tdir($tenant)."/notes_$ver.txt", $note);

        return $ver;
    }

    public function getEffective(string $tenant): string
    {
        $pf = $this->tdir($tenant).'/published.ver';
        if (!is_file($pf)) {
            return '';
        }
        $ver = trim((string) file_get_contents($pf));
        $vf = $this->tdir($tenant)."/versions/$ver.pel";

        return is_file($vf) ? (string) file_get_contents($vf) : '';
    }
}
