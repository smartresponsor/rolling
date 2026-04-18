CREATE TABLE IF NOT EXISTS role_audit (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    ts INTEGER NOT NULL,
    subject_id TEXT NOT NULL,
    action TEXT NOT NULL,
    scope_key TEXT NOT NULL,
    decision TEXT NOT NULL,
    reason TEXT DEFAULT '',
    obligations TEXT DEFAULT '{}',
    ctx TEXT DEFAULT '{}'
);
