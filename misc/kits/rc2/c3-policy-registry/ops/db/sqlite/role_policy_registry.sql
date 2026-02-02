CREATE TABLE IF NOT EXISTS role_policy_rev
(
    id INTEGER PRIMARY KEY CHECK
(
    id = 1
),
    rev INTEGER NOT NULL
    );
INSERT
OR IGNORE INTO role_policy_rev(id, rev) VALUES (1, 0);

CREATE TABLE IF NOT EXISTS role_policy
(
    id
    INTEGER
    PRIMARY
    KEY
    AUTOINCREMENT,
    ns
    TEXT
    NOT
    NULL,
    name
    TEXT
    NOT
    NULL,
    version
    TEXT
    NOT
    NULL,
    doc
    TEXT
    NOT
    NULL, -- JSON
    created_at
    INTEGER
    NOT
    NULL,
    is_active
    INTEGER
    NOT
    NULL
    DEFAULT
    0,
    UNIQUE
(
    ns,
    name,
    version
)
    );
CREATE INDEX IF NOT EXISTS idx_role_policy_name ON role_policy(ns, name);
CREATE INDEX IF NOT EXISTS idx_role_policy_active ON role_policy(ns, name, is_active);

CREATE TABLE IF NOT EXISTS role_policy_migration
(
    id
    INTEGER
    PRIMARY
    KEY
    AUTOINCREMENT,
    ns
    TEXT
    NOT
    NULL,
    name
    TEXT
    NOT
    NULL,
    from_version
    TEXT
    NOT
    NULL,
    to_version
    TEXT
    NOT
    NULL,
    note
    TEXT,
    steps
    TEXT, -- JSON
    applied_at
    INTEGER
    NOT
    NULL
);
