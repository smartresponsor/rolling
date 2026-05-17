# Audit & Explain v2

- DTOs: AuditDecisionInputDto, AuditDecisionResultDto, AuditDecisionRecordDto, AuditExplainNodeDto
- Interfaces: AuditLoggerInterface, ExplainerInterface
- Implementations: FileAuditRepository, SimpleAuditLogger, RuleExplainer
- Output: NDJSON audit (one record per decision) + structured explain tree

## Quick start

```
php tools/audit/explain_demo.php
cat report/explain_demo.json
cat report/audit.ndjson
```
