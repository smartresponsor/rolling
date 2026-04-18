<?php

declare(strict_types=1);

namespace App\Legacy\Policy\Registry;

use App\Legacy\Consistency\Policy\Token;
use RuntimeException;

/**
 *
 */

/**
 *
 */
final class InMemoryRegistryStore implements RegistryStoreInterface
{
    /** @var array ns => name => version => record */
    private array $db = [];
    /** @var array active version per (ns,name) */
    private array $active = [];
    /** @var array<string, array{from:string,to:string,note:?string,applied_at:int}[]> */
    private array $migrations = [];
    private int $rev = 0;

    /**
     * @param string $ns
     * @param string $name
     * @param string $version
     * @param string $docJson
     * @return \App\Legacy\Consistency\Policy\Token
     */
    public function put(string $ns, string $name, string $version, string $docJson): Token
    {
        $rec = new PolicyRecord($ns, $name, $version, $docJson, time(), false);
        $this->db[$ns][$name][$version] = $rec;
        $this->rev++;
        return new Token($this->rev);
    }

    /**
     * @param string $ns
     * @param string $name
     * @param string $version
     * @return \App\Legacy\Consistency\Policy\Token
     */
    public function activate(string $ns, string $name, string $version): Token
    {
        if (!isset($this->db[$ns][$name][$version])) {
            throw new RuntimeException('version not found');
        }
        // deactivate all
        foreach ($this->db[$ns][$name] ?? [] as $v => $rec) {
            $this->db[$ns][$name][$v]->isActive = false;
        }
        // activate
        $this->db[$ns][$name][$version]->isActive = true;
        $this->active[$ns][$name] = $version;
        $this->rev++;
        return new Token($this->rev);
    }

    /**
     * @param string $ns
     * @param string $name
     * @return \Policy\Role\Registry\PolicyRecord|null
     */
    public function getActive(string $ns, string $name): ?PolicyRecord
    {
        $v = $this->active[$ns][$name] ?? null;
        return $v ? ($this->db[$ns][$name][$v] ?? null) : null;
    }

    /**
     * @param string $ns
     * @param string $name
     * @return array
     */
    public function listVersions(string $ns, string $name): array
    {
        return array_values($this->db[$ns][$name] ?? []);
    }

    /**
     * @param string $ns
     * @param string $name
     * @param string $version
     * @return string|null
     */
    public function export(string $ns, string $name, string $version): ?string
    {
        return $this->db[$ns][$name][$version]->docJson ?? null;
    }

    /**
     * @return \App\Legacy\Consistency\Policy\Token
     */
    public function currentToken(): Token
    {
        return new Token($this->rev);
    }

    /**
     * @param string $ns
     * @param string $name
     * @param string $from
     * @param string $to
     * @param string|null $note
     * @param string|null $stepsJson
     * @return void
     */
    public function recordMigration(string $ns, string $name, string $from, string $to, ?string $note = null, ?string $stepsJson = null): void
    {
        $k = $ns . ':' . $name;
        $this->migrations[$k] ??= [];
        $this->migrations[$k][] = ['from' => $from, 'to' => $to, 'note' => $note, 'applied_at' => time()];
    }

    /**
     * @param string $ns
     * @param string $name
     * @return array
     */
    public function listMigrations(string $ns, string $name): array
    {
        $k = $ns . ':' . $name;
        return $this->migrations[$k] ?? [];
    }
}
