<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Infra\Role\Approval;

use src\ServiceInterface\Role\Approval\ApprovalStoreInterface;

/**
 *
 */

/**
 *
 */
final class FileApprovalStore implements ApprovalStoreInterface
{
    private string $dir;

    /**
     * @param string $dir
     */
    public function __construct(string $dir = __DIR__ . '/../../../../var/approval')
    {
        $this->dir = $dir;
        if (!is_dir($this->dir)) {
            @mkdir($this->dir, 0775, true);
        }
    }

    /**
     * @param string $id
     * @return string
     */
    private function path(string $id): string
    {
        return $this->dir . '/case_' . $id . '.json';
    }

    /**
     * @return string
     */
    private function makeId(): string
    {
        $t = microtime(true);
        try {
            $rand = random_int(0, 1_000_000);
        } catch (\Exception $e) {
        }
        return dechex((int) ($t * 1000)) . dechex($rand);
    }

    /**
     * @param array $case
     * @return string
     */
    public function create(array $case): string
    {
        $id = $this->makeId();
        $rec = [
            'id' => $id,
            'ts' => date('c'),
            'state' => 'pending',
            'case' => $case,
            'by' => null,
        ];
        file_put_contents($this->path($id), json_encode($rec, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        return $id;
    }

    /**
     * @param string $id
     * @return array|null
     */
    public function read(string $id): ?array
    {
        $p = $this->path($id);
        if (!is_file($p)) {
            return null;
        }
        return json_decode((string) file_get_contents($p), true);
    }

    /**
     * @param string $id
     * @param array $by
     * @return void
     */
    public function approve(string $id, array $by): void
    {
        $rec = $this->read($id);
        if (!$rec) {
            return;
        }
        $rec['state'] = 'approved';
        $rec['by'] = ['id' => $by['id'] ?? null, 'reason' => $by['reason'] ?? null, 'ts' => date('c')];
        file_put_contents($this->path($id), json_encode($rec, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }

    /**
     * @param string $id
     * @param array $by
     * @return void
     */
    public function reject(string $id, array $by): void
    {
        $rec = $this->read($id);
        if (!$rec) {
            return;
        }
        $rec['state'] = 'rejected';
        $rec['by'] = ['id' => $by['id'] ?? null, 'reason' => $by['reason'] ?? null, 'ts' => date('c')];
        file_put_contents($this->path($id), json_encode($rec, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }
}
