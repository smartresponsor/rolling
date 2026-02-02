CREATE TABLE IF NOT EXISTS role_rev
(
    id INTEGER PRIMARY KEY CHECK
(
    id = 1
),
    rev INTEGER NOT NULL
    );
INSERT
OR IGNORE INTO role_rev(id, rev) VALUES (1, 0);

CREATE TABLE IF NOT EXISTS role_tuple
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
    obj_type
    TEXT
    NOT
    NULL,
    obj_id
    TEXT
    NOT
    NULL,
    relation
    TEXT
    NOT
    NULL,
    subj_type
    TEXT
    NOT
    NULL,
    subj_id
    TEXT
    NOT
    NULL,
    subj_rel
    TEXT
);
CREATE INDEX IF NOT EXISTS idx_role_tuple_obj ON role_tuple(ns, obj_type, obj_id, relation);
CREATE INDEX IF NOT EXISTS idx_role_tuple_subj ON role_tuple(ns, subj_type, subj_id, subj_rel);
