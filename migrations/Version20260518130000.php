<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Creates the Rolling ACL system-storage tables used by Administering permission decisions.
 */
final class Version20260518130000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Rolling rolling_* ACL system tables for SQLite/system storage.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE IF NOT EXISTS rolling_permission (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, permission_key VARCHAR(180) NOT NULL, component_name VARCHAR(120) NOT NULL, description CLOB DEFAULT NULL)');
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS uniq_rolling_permission_key ON rolling_permission (permission_key)');

        $this->addSql('CREATE TABLE IF NOT EXISTS rolling_role (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, role_key VARCHAR(160) NOT NULL, label VARCHAR(180) NOT NULL, system_role BOOLEAN NOT NULL, enabled BOOLEAN NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS uniq_rolling_role_key ON rolling_role (role_key)');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_rolling_role_enabled ON rolling_role (enabled)');

        $this->addSql("CREATE TABLE IF NOT EXISTS rolling_acl_rule (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, subject_identifier VARCHAR(220) NOT NULL, permission_key VARCHAR(180) NOT NULL, scope_key VARCHAR(220) NOT NULL, effect VARCHAR(20) NOT NULL, conditions CLOB NOT NULL --(DC2Type:json)
, enabled BOOLEAN NOT NULL)");
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_rolling_acl_rule_subject ON rolling_acl_rule (subject_identifier)');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_rolling_acl_rule_permission ON rolling_acl_rule (permission_key)');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_rolling_acl_rule_subject_permission ON rolling_acl_rule (subject_identifier, permission_key)');

        $this->addSql('CREATE TABLE IF NOT EXISTS rolling_subject_role_assignment (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, subject_identifier VARCHAR(220) NOT NULL, role_key VARCHAR(160) NOT NULL, scope_key VARCHAR(220) NOT NULL, assigned_at DATETIME NOT NULL --(DC2Type:datetime_immutable))');
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS uniq_rolling_subject_role_scope ON rolling_subject_role_assignment (subject_identifier, role_key, scope_key)');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_rolling_subject_role_subject ON rolling_subject_role_assignment (subject_identifier)');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_rolling_subject_role_role ON rolling_subject_role_assignment (role_key)');

        $this->addSql('CREATE TABLE IF NOT EXISTS rolling_role_permission (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, role_key VARCHAR(160) NOT NULL, permission_key VARCHAR(180) NOT NULL, scope_pattern VARCHAR(220) NOT NULL, effect VARCHAR(20) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS uniq_rolling_role_permission ON rolling_role_permission (role_key, permission_key, scope_pattern)');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_rolling_role_permission_role ON rolling_role_permission (role_key)');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_rolling_role_permission_permission ON rolling_role_permission (permission_key)');

        $this->addSql('CREATE TABLE IF NOT EXISTS rolling_role_hierarchy (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, parent_role_key VARCHAR(160) NOT NULL, child_role_key VARCHAR(160) NOT NULL, enabled BOOLEAN NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS uniq_rolling_role_hierarchy_edge ON rolling_role_hierarchy (parent_role_key, child_role_key)');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_rolling_role_hierarchy_parent ON rolling_role_hierarchy (parent_role_key)');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_rolling_role_hierarchy_child ON rolling_role_hierarchy (child_role_key)');

        $this->addSql("CREATE TABLE IF NOT EXISTS rolling_acl_mutation_execution_event (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, request_key VARCHAR(180) NOT NULL, mutation_type VARCHAR(80) NOT NULL, subject_identifier VARCHAR(220) NOT NULL, permission_or_role_key VARCHAR(180) NOT NULL, scope_key VARCHAR(220) NOT NULL, requested_by_subject VARCHAR(220) NOT NULL, status VARCHAR(40) NOT NULL, succeeded BOOLEAN NOT NULL, safe_message CLOB NOT NULL, safe_context CLOB NOT NULL --(DC2Type:json)
, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
)");
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_rolling_acl_execution_request_key ON rolling_acl_mutation_execution_event (request_key)');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_rolling_acl_execution_mutation_type ON rolling_acl_mutation_execution_event (mutation_type)');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_rolling_acl_execution_subject ON rolling_acl_mutation_execution_event (subject_identifier)');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_rolling_acl_execution_status ON rolling_acl_mutation_execution_event (status)');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_rolling_acl_execution_created_at ON rolling_acl_mutation_execution_event (created_at)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS rolling_acl_mutation_execution_event');
        $this->addSql('DROP TABLE IF EXISTS rolling_role_hierarchy');
        $this->addSql('DROP TABLE IF EXISTS rolling_role_permission');
        $this->addSql('DROP TABLE IF EXISTS rolling_subject_role_assignment');
        $this->addSql('DROP TABLE IF EXISTS rolling_acl_rule');
        $this->addSql('DROP TABLE IF EXISTS rolling_role');
        $this->addSql('DROP TABLE IF EXISTS rolling_permission');
    }
}
