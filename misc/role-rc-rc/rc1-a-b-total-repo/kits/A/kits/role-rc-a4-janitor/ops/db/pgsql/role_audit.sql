CREATE TABLE IF NOT EXISTS role_audit
(
    id
    BIGSERIAL
    PRIMARY
    KEY,
    ts
    TIMESTAMPTZ
    NOT
    NULL
    DEFAULT
    NOW
(
),
    subject_id TEXT NOT NULL,
    action TEXT NOT NULL,
    scope_key TEXT NOT NULL,
    decision TEXT NOT NULL,
    reason TEXT,
    obligations JSONB,
    ctx JSONB
    );
CREATE INDEX IF NOT EXISTS idx_role_audit_ts ON role_audit(ts);
CREATE INDEX IF NOT EXISTS idx_role_audit_subject ON role_audit(subject_id);
