<?php

declare(strict_types=1);

namespace App\Infrastructure\Policy\Registry;

use App\Service\Consistency\Policy\Token;
use RuntimeException;

final class InMemoryStore implements StoreInterface
{
    /** @var array<string, array<string, array<string, PolicyRecord>>> */
    private array $db = [];

    /** @var array<string, array<string, string>> */
    private array $active = [];

    /** @var array<string, list<array{from:string,to:string,note:?string,applied_at:int}>> */
    private array $migrations = [];

    private int $rev = 0;

    public function put(string $ns, string $name, string $version, string $docJson): Token
    {
        $this->db[$ns][$name][$version] = new PolicyRecord($ns, $name, $version, $docJson, time(), false);
        $this->rev++;

        return new Token($this->rev);
    }

    public function activate(string $ns, string $name, string $version): Token
    {
        if (!isset($this->db[$ns][$name][$version])) {
            throw new RuntimeException('version not found');
        }

        foreach ($this->db[$ns][$name] ?? [] as $candidateVersion => $record) {
            $this->db[$ns][$name][$candidateVersion]->isActive = false;
        }

        $this->db[$ns][$name][$version]->isActive = true;
        $this->active[$ns][$name] = $version;
        $this->rev++;

        return new Token($this->rev);
    }

    public function getActive(string $ns, string $name): ?PolicyRecord
    {
        $version = $this->active[$ns][$name] ?? null;

        return $version !== null ? ($this->db[$ns][$name][$version] ?? null) : null;
    }

    public function listVersions(string $ns, string $name): array
    {
        return array_values($this->db[$ns][$name] ?? []);
    }

    public function export(string $ns, string $name, string $version): ?string
    {
        return $this->db[$ns][$name][$version]->docJson ?? null;
    }

    public function currentToken(): Token
    {
        return new Token($this->rev);
    }

    public function recordMigration(string $ns, string $name, string $from, string $to, ?string $note = null, ?string $stepsJson = null): void
    {
        $key = $ns . ':' . $name;
        $this->migrations[$key] ??= [];
        $this->migrations[$key][] = ['from' => $from, 'to' => $to, 'note' => $note, 'applied_at' => time()];
    }

    public function listMigrations(string $ns, string $name): array
    {
        return $this->migrations[$ns . ':' . $name] ?? [];
    }
}
