<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Policy\Registry;

use App\Rolling\Service\Consistency\Policy\PolicyConsistencyToken;

final class PdoStore implements StoreInterface
{
    public function __construct(private readonly \PDO $pdo)
    {
    }

    public function put(string $ns, string $nameEntity, string $version, string $docJson): PolicyConsistencyToken
    {
        $statement = $this->pdo->prepare('INSERT INTO role_policy(ns,nameEntity,version,doc,created_at,is_active) VALUES(?,?,?,?,?,0)');
        $statement->execute([$ns, $nameEntity, $version, $docJson, time()]);
        $this->bumpRev();

        return $this->currentToken();
    }

    public function activate(string $ns, string $nameEntity, string $version): PolicyConsistencyToken
    {
        $this->pdo->beginTransaction();

        try {
            $deactivate = $this->pdo->prepare('UPDATE role_policy SET is_active=0 WHERE ns=? AND nameEntity=?');
            $deactivate->execute([$ns, $nameEntity]);
            $activate = $this->pdo->prepare('UPDATE role_policy SET is_active=1 WHERE ns=? AND nameEntity=? AND version=?');
            $activate->execute([$ns, $nameEntity, $version]);
            if (0 === $activate->rowCount()) {
                throw new \RuntimeException('version not found');
            }
            $this->bumpRev();
            $this->pdo->commit();
        } catch (\Throwable $throwable) {
            $this->pdo->rollBack();
            throw $throwable;
        }

        return $this->currentToken();
    }

    public function getActive(string $ns, string $nameEntity): ?PolicyRecord
    {
        $statement = $this->pdo->prepare('SELECT ns,nameEntity,version,doc,created_at,is_active FROM role_policy WHERE ns=? AND nameEntity=? AND is_active=1 LIMIT 1');
        $statement->execute([$ns, $nameEntity]);
        $row = $statement->fetch(\PDO::FETCH_ASSOC);

        if (false === $row) {
            return null;
        }

        return new PolicyRecord((string) $row['ns'], (string) $row['nameEntity'], (string) $row['version'], (string) $row['doc'], (int) $row['created_at'], (bool) $row['is_active']);
    }

    public function listVersions(string $ns, string $nameEntity): array
    {
        $statement = $this->pdo->prepare('SELECT ns,nameEntity,version,doc,created_at,is_active FROM role_policy WHERE ns=? AND nameEntity=? ORDER BY created_at ASC');
        $statement->execute([$ns, $nameEntity]);
        $records = [];
        while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            $records[] = new PolicyRecord((string) $row['ns'], (string) $row['nameEntity'], (string) $row['version'], (string) $row['doc'], (int) $row['created_at'], (bool) $row['is_active']);
        }

        return $records;
    }

    public function export(string $ns, string $nameEntity, string $version): ?string
    {
        $statement = $this->pdo->prepare('SELECT doc FROM role_policy WHERE ns=? AND nameEntity=? AND version=?');
        $statement->execute([$ns, $nameEntity, $version]);
        $doc = $statement->fetchColumn();

        return false !== $doc ? (string) $doc : null;
    }

    public function currentToken(): PolicyConsistencyToken
    {
        $rev = (int) $this->pdo->query('SELECT rev FROM role_policy_rev WHERE id=1')->fetchColumn();

        return new PolicyConsistencyToken($rev);
    }

    public function recordMigration(string $ns, string $nameEntity, string $from, string $to, ?string $note = null, ?string $stepsJson = null): void
    {
        $statement = $this->pdo->prepare('INSERT INTO role_policy_migration(ns,nameEntity,from_version,to_version,note,steps,applied_at) VALUES(?,?,?,?,?,?,?)');
        $statement->execute([$ns, $nameEntity, $from, $to, $note, $stepsJson, time()]);
    }

    public function listMigrations(string $ns, string $nameEntity): array
    {
        $statement = $this->pdo->prepare('SELECT from_version, to_version, note, applied_at FROM role_policy_migration WHERE ns=? AND nameEntity=? ORDER BY applied_at ASC');
        $statement->execute([$ns, $nameEntity]);
        $migrations = [];
        while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            $migrations[] = ['from' => (string) $row['from_version'], 'to' => (string) $row['to_version'], 'note' => null !== $row['note'] ? (string) $row['note'] : null, 'applied_at' => (int) $row['applied_at']];
        }

        return $migrations;
    }

    private function bumpRev(): void
    {
        $this->pdo->exec('UPDATE role_policy_rev SET rev = rev + 1 WHERE id=1');
    }
}
