<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Policy\Registry;

use App\Rolling\Service\Consistency\Policy\PolicyConsistencyToken;

final class InMemoryStore implements StoreInterface
{
    /** @var array<string, array<string, array<string, PolicyRecord>>> */
    private array $db = [];

    /** @var array<string, array<string, string>> */
    private array $active = [];

    /** @var array<string, list<array{from: string, to: string, migrationNote: ?string, appliedAt: int}>> */
    private array $migrations = [];

    private int $rev = 0;

    public function put(string $ns, string $nameEntity, string $version, string $docJson): PolicyConsistencyToken
    {
        $this->db[$ns][$nameEntity][$version] = new PolicyRecord($ns, $nameEntity, $version, $docJson, time(), false);
        ++$this->rev;

        return new PolicyConsistencyToken($this->rev);
    }

    public function activate(string $ns, string $nameEntity, string $version): PolicyConsistencyToken
    {
        if (!isset($this->db[$ns][$nameEntity][$version])) {
            throw new \RuntimeException('version not found');
        }

        foreach ($this->db[$ns][$nameEntity] ?? [] as $candidateVersion => $record) {
            $this->db[$ns][$nameEntity][$candidateVersion]->isActive = false;
        }

        $this->db[$ns][$nameEntity][$version]->isActive = true;
        $this->active[$ns][$nameEntity] = $version;
        ++$this->rev;

        return new PolicyConsistencyToken($this->rev);
    }

    public function getActive(string $ns, string $nameEntity): ?PolicyRecord
    {
        $version = $this->active[$ns][$nameEntity] ?? null;

        return null !== $version ? ($this->db[$ns][$nameEntity][$version] ?? null) : null;
    }

    public function listVersions(string $ns, string $nameEntity): array
    {
        return array_values($this->db[$ns][$nameEntity] ?? []);
    }

    public function export(string $ns, string $nameEntity, string $version): ?string
    {
        return $this->db[$ns][$nameEntity][$version]->docJson ?? null;
    }

    public function currentToken(): PolicyConsistencyToken
    {
        return new PolicyConsistencyToken($this->rev);
    }

    public function recordMigration(string $ns, string $nameEntity, string $from, string $to, ?string $note = null, ?string $stepsJson = null): void
    {
        $key = $ns.':'.$nameEntity;
        $this->migrations[$key] ??= [];
        $this->migrations[$key][] = [
            'from' => $from,
            'to' => $to,
            'migrationNote' => $note,
            'appliedAt' => time(),
        ];
    }

    public function listMigrations(string $ns, string $nameEntity): array
    {
        return $this->migrations[$ns.':'.$nameEntity] ?? [];
    }
}
