<?php

declare(strict_types=1);

namespace App\Infrastructure\Policy\Registry;

use App\Service\Consistency\Policy\Token;
use PDO;
use RuntimeException;
use Throwable;

final class PdoStore implements StoreInterface
{
    public function __construct(private readonly PDO $pdo) {}

    public function put(string $ns, string $name, string $version, string $docJson): Token
    {
        $statement = $this->pdo->prepare('INSERT INTO role_policy(ns,name,version,doc,created_at,is_active) VALUES(?,?,?,?,?,0)');
        $statement->execute([$ns, $name, $version, $docJson, time()]);
        $this->bumpRev();

        return $this->currentToken();
    }

    public function activate(string $ns, string $name, string $version): Token
    {
        $this->pdo->beginTransaction();

        try {
            $deactivate = $this->pdo->prepare('UPDATE role_policy SET is_active=0 WHERE ns=? AND name=?');
            $deactivate->execute([$ns, $name]);
            $activate = $this->pdo->prepare('UPDATE role_policy SET is_active=1 WHERE ns=? AND name=? AND version=?');
            $activate->execute([$ns, $name, $version]);
            if ($activate->rowCount() === 0) {
                throw new RuntimeException('version not found');
            }
            $this->bumpRev();
            $this->pdo->commit();
        } catch (Throwable $throwable) {
            $this->pdo->rollBack();
            throw $throwable;
        }

        return $this->currentToken();
    }

    public function getActive(string $ns, string $name): ?PolicyRecord
    {
        $statement = $this->pdo->prepare('SELECT ns,name,version,doc,created_at,is_active FROM role_policy WHERE ns=? AND name=? AND is_active=1 LIMIT 1');
        $statement->execute([$ns, $name]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        return new PolicyRecord((string) $row['ns'], (string) $row['name'], (string) $row['version'], (string) $row['doc'], (int) $row['created_at'], (bool) $row['is_active']);
    }

    public function listVersions(string $ns, string $name): array
    {
        $statement = $this->pdo->prepare('SELECT ns,name,version,doc,created_at,is_active FROM role_policy WHERE ns=? AND name=? ORDER BY created_at ASC');
        $statement->execute([$ns, $name]);
        $records = [];
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $records[] = new PolicyRecord((string) $row['ns'], (string) $row['name'], (string) $row['version'], (string) $row['doc'], (int) $row['created_at'], (bool) $row['is_active']);
        }

        return $records;
    }

    public function export(string $ns, string $name, string $version): ?string
    {
        $statement = $this->pdo->prepare('SELECT doc FROM role_policy WHERE ns=? AND name=? AND version=?');
        $statement->execute([$ns, $name, $version]);
        $doc = $statement->fetchColumn();

        return $doc !== false ? (string) $doc : null;
    }

    public function currentToken(): Token
    {
        $rev = (int) $this->pdo->query('SELECT rev FROM role_policy_rev WHERE id=1')->fetchColumn();

        return new Token($rev);
    }

    public function recordMigration(string $ns, string $name, string $from, string $to, ?string $note = null, ?string $stepsJson = null): void
    {
        $statement = $this->pdo->prepare('INSERT INTO role_policy_migration(ns,name,from_version,to_version,note,steps,applied_at) VALUES(?,?,?,?,?,?,?)');
        $statement->execute([$ns, $name, $from, $to, $note, $stepsJson, time()]);
    }

    public function listMigrations(string $ns, string $name): array
    {
        $statement = $this->pdo->prepare('SELECT from_version, to_version, note, applied_at FROM role_policy_migration WHERE ns=? AND name=? ORDER BY applied_at ASC');
        $statement->execute([$ns, $name]);
        $migrations = [];
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $migrations[] = ['from' => (string) $row['from_version'], 'to' => (string) $row['to_version'], 'note' => $row['note'] !== null ? (string) $row['note'] : null, 'applied_at' => (int) $row['applied_at']];
        }

        return $migrations;
    }

    private function bumpRev(): void
    {
        $this->pdo->exec('UPDATE role_policy_rev SET rev = rev + 1 WHERE id=1');
    }
}
