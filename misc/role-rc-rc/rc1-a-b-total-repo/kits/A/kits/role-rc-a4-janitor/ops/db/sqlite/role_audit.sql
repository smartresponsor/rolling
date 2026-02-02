CREATE TABLE IF NOT EXISTS role_audit
(
    id
    INTEGER
    PRIMARY
    KEY
    AUTOINCREMENT,
    ts
    INTEGER
    NOT
    NULL,
    subject_id
    TEXT
    NOT
    NULL,
    action
    TEXT
    NOT
    NULL,
    scope_key
    TEXT
    NOT
    NULL,
    decision
    TEXT
    NOT
    NULL,
    reason
    TEXT,
    obligations
    TEXT,
    ctx
    TEXT
);
CREATE INDEX IF NOT EXISTS idx_role_audit_ts ON role_audit(ts);
CREATE INDEX IF NOT EXISTS idx_role_audit_subject ON role_audit(subject_id);
