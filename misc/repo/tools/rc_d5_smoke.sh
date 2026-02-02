#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
php -l "$ROOT/src/Service/Role/Audit/Redactor.php" >/dev/null
php -l "$ROOT/src/Service/Role/Audit/Logger.php" >/dev/null
# emit sample
php -r 'require ""$ROOT"/src/Service/Role/Audit/Redactor.php"; require ""$ROOT"/src/Service/Role/Audit/Logger.php"; use App\Service\Role\Audit\Logger; $l=new Logger(""$ROOT"/var/log/role"); $e=["trace"=>"t-1","tenant"=>"t1","subject"=>"user:1","resource"=>"doc:42","relation"=>"viewer","context"=>["ip"=>"1.2.3.4","ssn"=>"123-45-6789","notes"=>"abc token_deadbeef xyz"],"effect"=>"allow","reason"=>"cache"]; $o=["mask"=>["context.ssn","resource"],"redact"=>[["path"=>"context.notes","pattern"=>"\\\token_[a-z0-9]+\\\"]]]; $res=$l->write($e,$o); echo json_encode($res),"\n";'
php "$ROOT/tools/audit_dump.php" 5 >/dev/null
echo "RC-D5 smoke OK"
