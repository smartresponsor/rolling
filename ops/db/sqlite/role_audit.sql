CREATE TABLE role_audit (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    ts INTEGER NOT NULL,
    subject_id TEXT NOT NULL,
    action TEXT NOT NULL,
    scope_key TEXT NOT NULL,
    decision TEXT NOT NULL,
    reason TEXT NOT NULL,
    obligations TEXT NOT NULL,
    ctx TEXT NOT NULL
);
