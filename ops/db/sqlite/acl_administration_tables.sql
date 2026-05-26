-- Rolling ACL administration persistence baseline for the system SQLite entity manager.
-- These tables are intentionally prefixed with rolling_ and contain no user secrets.

CREATE TABLE IF NOT EXISTS rolling_role (
    id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    role_key VARCHAR(160) NOT NULL,
    label VARCHAR(180) NOT NULL,
    system_role BOOLEAN NOT NULL DEFAULT 0,
    enabled BOOLEAN NOT NULL DEFAULT 1
);
CREATE UNIQUE INDEX IF NOT EXISTS uniq_rolling_role_key ON rolling_role (role_key);

CREATE TABLE IF NOT EXISTS rolling_permission (
    id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    permission_key VARCHAR(180) NOT NULL,
    component_name VARCHAR(120) NOT NULL,
    description CLOB DEFAULT NULL
);
CREATE UNIQUE INDEX IF NOT EXISTS uniq_rolling_permission_key ON rolling_permission (permission_key);

CREATE TABLE IF NOT EXISTS rolling_role_permission (
    id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    role_key VARCHAR(160) NOT NULL,
    permission_key VARCHAR(180) NOT NULL,
    scope_pattern VARCHAR(220) NOT NULL DEFAULT 'global',
    effect VARCHAR(20) NOT NULL DEFAULT 'allow'
);
CREATE UNIQUE INDEX IF NOT EXISTS uniq_rolling_role_permission ON rolling_role_permission (role_key, permission_key, scope_pattern);

CREATE TABLE IF NOT EXISTS rolling_subject_role_assignment (
    id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    subject_identifier VARCHAR(220) NOT NULL,
    role_key VARCHAR(160) NOT NULL,
    scope_key VARCHAR(220) NOT NULL DEFAULT 'global',
    assigned_at DATETIME NOT NULL
);
CREATE UNIQUE INDEX IF NOT EXISTS uniq_rolling_subject_role_scope ON rolling_subject_role_assignment (subject_identifier, role_key, scope_key);

CREATE TABLE IF NOT EXISTS rolling_acl_rule (
    id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    subject_identifier VARCHAR(220) NOT NULL,
    permission_key VARCHAR(180) NOT NULL,
    scope_key VARCHAR(220) NOT NULL DEFAULT 'global',
    effect VARCHAR(20) NOT NULL DEFAULT 'allow',
    conditions CLOB NOT NULL DEFAULT '{}',
    enabled BOOLEAN NOT NULL DEFAULT 1
);
CREATE INDEX IF NOT EXISTS idx_rolling_acl_rule_subject ON rolling_acl_rule (subject_identifier);
CREATE INDEX IF NOT EXISTS idx_rolling_acl_rule_permission ON rolling_acl_rule (permission_key);

CREATE TABLE IF NOT EXISTS rolling_acl_mutation_execution_event (
    id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    request_key VARCHAR(180) NOT NULL,
    mutation_type VARCHAR(80) NOT NULL,
    subject_identifier VARCHAR(220) NOT NULL,
    permission_or_role_key VARCHAR(180) NOT NULL,
    scope_key VARCHAR(220) NOT NULL DEFAULT 'global',
    requested_by_subject VARCHAR(220) NOT NULL,
    status VARCHAR(40) NOT NULL,
    succeeded BOOLEAN NOT NULL,
    safe_message CLOB NOT NULL,
    safe_context CLOB NOT NULL,
    created_at DATETIME NOT NULL
);

CREATE INDEX IF NOT EXISTS idx_rolling_acl_execution_request_key ON rolling_acl_mutation_execution_event (request_key);
CREATE INDEX IF NOT EXISTS idx_rolling_acl_execution_mutation_type ON rolling_acl_mutation_execution_event (mutation_type);
CREATE INDEX IF NOT EXISTS idx_rolling_acl_execution_subject ON rolling_acl_mutation_execution_event (subject_identifier);
CREATE INDEX IF NOT EXISTS idx_rolling_acl_execution_status ON rolling_acl_mutation_execution_event (status);
CREATE INDEX IF NOT EXISTS idx_rolling_acl_execution_created_at ON rolling_acl_mutation_execution_event (created_at);
