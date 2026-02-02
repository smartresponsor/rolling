CREATE TABLE IF NOT EXISTS role_policy_rev
(
    id INT PRIMARY KEY CHECK
(
    id = 1
),
    rev BIGINT NOT NULL
    );
INSERT INTO role_policy_rev(id, rev)
VALUES (1, 0) ON CONFLICT (id) DO NOTHING;

CREATE TABLE IF NOT EXISTS role_policy
(
    id
    BIGSERIAL
    PRIMARY
    KEY,
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
    BIGINT
    NOT
    NULL,
    is_active
    INT
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
    BIGSERIAL
    PRIMARY
    KEY,
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
    TEXT
    NULL,
    steps
    TEXT
    NULL, -- JSON
    applied_at
    BIGINT
    NOT
    NULL
);
