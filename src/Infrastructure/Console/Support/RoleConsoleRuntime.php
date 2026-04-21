<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Console\Support;

use App\Rolling\Infrastructure\Housekeeping\Archive\JsonlAuditArchiver;
use App\Rolling\Infrastructure\Housekeeping\Janitor;
use App\Rolling\Infrastructure\Housekeeping\PdoAuditGc;
use App\Rolling\Infrastructure\Housekeeping\PdoReplayGc;
use App\Rolling\Infrastructure\Policy\Registry\InMemoryStore;
use App\Rolling\Infrastructure\Policy\Registry\PdoStore;
use App\Rolling\Infrastructure\Policy\Registry\RegistryService;
use App\Rolling\Infrastructure\Rebac\InMemoryTupleStore;
use App\Rolling\Infrastructure\Rebac\PdoTupleStore;
use App\Rolling\Infrastructure\Rebac\Tuple;
use App\Rolling\InfrastructureInterface\Rebac\TupleStoreInterface;
use App\Rolling\Service\Admin\RebacStatsService;
use App\Rolling\Service\Rebac\Checker;
use App\Rolling\Service\Rebac\Writer;

final class RoleConsoleRuntime
{
    private ?RegistryService $policyServiceCache = null;
    private ?TupleStoreInterface $rebacStoreCache = null;
    private ?RebacStatsService $adminRebacStatsServiceCache = null;

    public function rolePolicyNs(): string
    {
        return $this->env('ROLE_POLICY_NS', 'default');
    }

    public function roleRebacNs(): string
    {
        return $this->env('ROLE_REBAC_NS', 'default');
    }

    public function roleAdminNs(): string
    {
        return $this->env('ROLE_ADMIN_NS', 'default');
    }

    public function policyService(): RegistryService
    {
        if ($this->policyServiceCache instanceof RegistryService) {
            return $this->policyServiceCache;
        }

        $dsn = getenv('ROLE_POLICY_DSN');
        $store = is_string($dsn) && '' !== $dsn
            ? new PdoStore(new \PDO($dsn))
            : new InMemoryStore();

        return $this->policyServiceCache = new RegistryService($store);
    }

    public function policyImport(string $name, string $version, string $docJson, ?string $ns = null): string
    {
        return (string) $this->policyService()->importPolicy($ns ?? $this->rolePolicyNs(), $name, $version, $docJson);
    }

    public function policyActivate(string $name, string $version, ?string $ns = null): string
    {
        return (string) $this->policyService()->activatePolicy($ns ?? $this->rolePolicyNs(), $name, $version);
    }

    public function policyExport(string $name, string $version, ?string $ns = null): ?string
    {
        return $this->policyService()->exportPolicy($ns ?? $this->rolePolicyNs(), $name, $version);
    }

    public function policyList(string $name, ?string $ns = null): array
    {
        return $this->policyService()->listVersions($ns ?? $this->rolePolicyNs(), $name);
    }

    public function policyMigrate(string $name, string $from, string $to, ?string $note = null, ?string $ns = null): void
    {
        $policyNs = $ns ?? $this->rolePolicyNs();
        $this->policyService()->recordMigration($policyNs, $name, $from, $to, $note);
        $this->policyService()->activatePolicy($policyNs, $name, $to);
    }

    public function rebacStore(): TupleStoreInterface
    {
        if ($this->rebacStoreCache instanceof TupleStoreInterface) {
            return $this->rebacStoreCache;
        }

        $dsn = getenv('ROLE_REBAC_DSN');
        if (is_string($dsn) && '' !== $dsn) {
            return $this->rebacStoreCache = new PdoTupleStore(new \PDO($dsn));
        }

        return $this->rebacStoreCache = new InMemoryTupleStore();
    }

    public function rebacWriter(): Writer
    {
        return new Writer($this->rebacStore());
    }

    public function rebacChecker(): Checker
    {
        return new Checker($this->rebacStore());
    }

    public function rebacTuple(string $objectType, string $objectId, string $relation, string $subjectType, string $subjectId): Tuple
    {
        return new Tuple(
            $this->roleRebacNs(),
            $objectType,
            $objectId,
            $relation,
            $subjectType,
            $subjectId,
            null,
        );
    }

    public function adminRebacStatsService(): RebacStatsService
    {
        if ($this->adminRebacStatsServiceCache instanceof RebacStatsService) {
            return $this->adminRebacStatsServiceCache;
        }

        $dsn = getenv('ROLE_REBAC_DSN');
        $store = is_string($dsn) && '' !== $dsn
            ? new PdoTupleStore(new \PDO($dsn))
            : new InMemoryTupleStore();

        return $this->adminRebacStatsServiceCache = new RebacStatsService($store);
    }

    public function janitorConfig(string $path): array
    {
        if ('' === $path || !is_file($path)) {
            return [];
        }

        $decoded = json_decode((string) file_get_contents($path), true);

        return is_array($decoded) ? $decoded : [];
    }

    public function janitor(string $dsn, string $configPath): Janitor
    {
        $pdo = new \PDO($dsn);
        if (str_starts_with($dsn, 'sqlite:')) {
            @$pdo->exec('CREATE TABLE IF NOT EXISTS role_audit(id INTEGER PRIMARY KEY AUTOINCREMENT, ts INTEGER, subject_id TEXT, action TEXT, scope_key TEXT, decision TEXT, reason TEXT, obligations TEXT, ctx TEXT)');
            @$pdo->exec('CREATE TABLE IF NOT EXISTS replay_nonce(nonce TEXT PRIMARY KEY, expires_ts INTEGER NOT NULL)');
        }

        return new Janitor($pdo, $this->janitorConfig($configPath));
    }

    public function auditDsn(): string
    {
        return $this->env('ROLE_AUDIT_DSN', 'sqlite::memory:');
    }

    public function retentionConfigPath(): string
    {
        return $this->env('ROLE_RETENTION_CONFIG', dirname(__DIR__, 4).'/ops/retention.json');
    }

    public function janitorPdo(string $dsn): \PDO
    {
        $pdo = new \PDO($dsn);
        if (str_starts_with($dsn, 'sqlite:')) {
            @$pdo->exec('CREATE TABLE IF NOT EXISTS role_audit(id INTEGER PRIMARY KEY AUTOINCREMENT, ts INTEGER, subject_id TEXT, action TEXT, scope_key TEXT, decision TEXT, reason TEXT, obligations TEXT, ctx TEXT)');
            @$pdo->exec('CREATE TABLE IF NOT EXISTS replay_nonce(nonce TEXT PRIMARY KEY, expires_ts INTEGER NOT NULL)');
        }

        return $pdo;
    }

    public function janitorAuditGc(string $dsn, int $days, int $batch): array
    {
        $pdo = $this->janitorPdo($dsn);
        $cut = time() - ($days * 86400);
        $deleted = (new PdoAuditGc($pdo))->deleteOlderThanEpoch($cut, $batch);

        return ['ok' => true, 'dsn' => $dsn, 'days' => $days, 'batch' => $batch, 'deleted' => $deleted];
    }

    public function janitorReplayGc(string $dsn, int $batch): array
    {
        $pdo = $this->janitorPdo($dsn);
        $deleted = (new PdoReplayGc($pdo))->deleteExpired(time(), $batch);

        return ['ok' => true, 'dsn' => $dsn, 'batch' => $batch, 'deleted' => $deleted];
    }

    public function janitorArchiveAudit(string $dsn, int $days, string $path, int $batch, bool $gzip): array
    {
        $pdo = $this->janitorPdo($dsn);
        $cut = time() - ($days * 86400);
        $result = (new JsonlAuditArchiver($pdo))->archiveOlderThanEpoch($cut, $path, $batch, $gzip);
        $result['ok'] = true;
        $result['dsn'] = $dsn;
        $result['days'] = $days;
        $result['batch'] = $batch;
        $result['gzip'] = $gzip;

        return $result;
    }

    private function env(string $name, string $default): string
    {
        $value = getenv($name);

        return is_string($value) && '' !== $value ? $value : $default;
    }
}
