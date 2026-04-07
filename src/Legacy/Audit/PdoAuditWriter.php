<?php
declare(strict_types=1);

namespace App\Legacy\Audit;

use PDO;
use PDOException;

/**
 *
 */

/**
 *
 */
final class PdoAuditWriter implements AuditWriter
{
    private PDO $pdo;
    private string $table;
    private bool $maskPII;

    /**
     * @param \PDO $pdo
     * @param string $table
     * @param bool $maskPII
     */
    public function __construct(PDO $pdo, string $table = 'role_audit', bool $maskPII = true)
    {
        $this->pdo = $pdo;
        $this->table = $table;
        $this->maskPII = $maskPII;
    }

    /**
     * @param \App\Legacy\Audit\AuditRecord $rec
     * @return void
     */
    public function write(AuditRecord $rec): void
    {
        $ctx = $this->maskPII ? $this->maskedContext($rec->context) : $rec->context;
        $sql = "INSERT INTO {$this->table} (ts, subject_id, action, scope_key, decision, reason, obligations, ctx)
                VALUES (:ts, :subject_id, :action, :scope_key, :decision, :reason, :obligations, :ctx)";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':ts', $rec->ts);
            $stmt->bindValue(':subject_id', $rec->subjectId);
            $stmt->bindValue(':action', $rec->action);
            $stmt->bindValue(':scope_key', $rec->scopeKey);
            $stmt->bindValue(':decision', $rec->decision);
            $stmt->bindValue(':reason', $rec->reason);
            $stmt->bindValue(':obligations', json_encode($rec->obligations, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            $stmt->bindValue(':ctx', json_encode($ctx, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            $stmt->execute();
        } catch (PDOException $e) {
            error_log('PdoAuditWriter::write failure: ' . $e->getMessage());
        }
    }

    /**
     * @param array $ctx @return array<string,mixed>
     * @return array
     */
    private function maskedContext(array $ctx): array
    {
        $out = $ctx;
        foreach (['secret', 'token', 'password', 'email'] as $k) {
            if (array_key_exists($k, $out)) $out[$k] = '***';
        }
        if (isset($out['ip'])) {
            // обрежем до /24 для IPv4, иначе просто маска
            if (preg_match('/^\d+\.\d+\.\d+\.\d+$/', (string)$out['ip'])) {
                $parts = explode('.', (string)$out['ip']);
                $out['ip'] = $parts[0] . '.' . $parts[1] . '.' . $parts[2] . '.0/24';
            } else {
                $out['ip'] = '***';
            }
        }
        return $out;
    }
}
