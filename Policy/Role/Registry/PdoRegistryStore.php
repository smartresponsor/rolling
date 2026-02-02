<?php
declare(strict_types=1);

namespace Policy\Role\Registry;

use App\Consistency\Role\Policy\Token;
use PDO;
use RuntimeException;
use Throwable;

/**
 *
 */

/**
 *
 */
final class PdoRegistryStore implements RegistryStoreInterface
{
    /**
     * @param \PDO $pdo
     */
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * @param string $ns
     * @param string $name
     * @param string $version
     * @param string $docJson
     * @return \App\Consistency\Role\Policy\Token
     */
    public function put(string $ns, string $name, string $version, string $docJson): Token
    {
        $st = $this->pdo->prepare("INSERT INTO role_policy(ns,name,version,doc,created_at,is_active) VALUES(?,?,?,?,?,0)");
        $st->execute([$ns, $name, $version, $docJson, time()]);
        $this->bumpRev();
        return $this->currentToken();
    }

    /**
     * @throws \Throwable
     */
    /**
     * @param string $ns
     * @param string $name
     * @param string $version
     * @return \App\Consistency\Role\Policy\Token
     * @throws \Throwable
     */
    public function activate(string $ns, string $name, string $version): Token
    {
        $this->pdo->beginTransaction();
        try {
            $de = $this->pdo->prepare("UPDATE role_policy SET is_active=0 WHERE ns=? AND name=?");
            $de->execute([$ns, $name]);
            $ac = $this->pdo->prepare("UPDATE role_policy SET is_active=1 WHERE ns=? AND name=? AND version=?");
            $ac->execute([$ns, $name, $version]);
            if ($ac->rowCount() === 0) {
                throw new RuntimeException("version not found");
            }
            $this->bumpRev();
            $this->pdo->commit();
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
        return $this->currentToken();
    }

    /**
     * @param string $ns
     * @param string $name
     * @return \Policy\Role\Registry\PolicyRecord|null
     */
    public function getActive(string $ns, string $name): ?PolicyRecord
    {
        $st = $this->pdo->prepare("SELECT ns,name,version,doc,created_at,is_active FROM role_policy WHERE ns=? AND name=? AND is_active=1 LIMIT 1");
        $st->execute([$ns, $name]);
        $r = $st->fetch(PDO::FETCH_ASSOC);
        if (!$r) return null;
        return new PolicyRecord((string)$r['ns'], (string)$r['name'], (string)$r['version'], (string)$r['doc'], (int)$r['created_at'], (bool)$r['is_active']);
    }

    /**
     * @param string $ns
     * @param string $name
     * @return array
     */
    public function listVersions(string $ns, string $name): array
    {
        $st = $this->pdo->prepare("SELECT ns,name,version,doc,created_at,is_active FROM role_policy WHERE ns=? AND name=? ORDER BY created_at ASC");
        $st->execute([$ns, $name]);
        $out = [];
        while ($r = $st->fetch(PDO::FETCH_ASSOC)) {
            $out[] = new PolicyRecord((string)$r['ns'], (string)$r['name'], (string)$r['version'], (string)$r['doc'], (int)$r['created_at'], (bool)$r['is_active']);
        }
        return $out;
    }

    /**
     * @param string $ns
     * @param string $name
     * @param string $version
     * @return string|null
     */
    public function export(string $ns, string $name, string $version): ?string
    {
        $st = $this->pdo->prepare("SELECT doc FROM role_policy WHERE ns=? AND name=? AND version=?");
        $st->execute([$ns, $name, $version]);
        $doc = $st->fetchColumn();
        return $doc !== false ? (string)$doc : null;
    }

    /**
     * @return \App\Consistency\Role\Policy\Token
     */
    public function currentToken(): Token
    {
        $rev = (int)$this->pdo->query("SELECT rev FROM role_policy_rev WHERE id=1")->fetchColumn();
        return new Token($rev);
    }

    /**
     * @return void
     */
    private function bumpRev(): void
    {
        $this->pdo->exec("UPDATE role_policy_rev SET rev = rev + 1 WHERE id=1");
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
        $st = $this->pdo->prepare("INSERT INTO role_policy_migration(ns,name,from_version,to_version,note,steps,applied_at) VALUES(?,?,?,?,?,?,?)");
        $st->execute([$ns, $name, $from, $to, $note, $stepsJson, time()]);
    }

    /**
     * @param string $ns
     * @param string $name
     * @return array
     */
    public function listMigrations(string $ns, string $name): array
    {
        $st = $this->pdo->prepare("SELECT from_version, to_version, note, applied_at FROM role_policy_migration WHERE ns=? AND name=? ORDER BY applied_at ASC");
        $st->execute([$ns, $name]);
        $out = [];
        while ($r = $st->fetch(PDO::FETCH_ASSOC)) {
            $out[] = ['from' => (string)$r['from_version'], 'to' => (string)$r['to_version'], 'note' => $r['note'] !== null ? (string)$r['note'] : null, 'applied_at' => (int)$r['applied_at']];
        }
        return $out;
    }
}
