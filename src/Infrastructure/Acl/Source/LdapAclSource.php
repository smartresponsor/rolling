<?php

declare(strict_types=1);

namespace App\Infrastructure\Acl\Source;

use App\InfrastructureInterface\Acl\AclSourceInterface;
use App\Entity\Role\Scope;
use App\Entity\Role\SubjectId;

/**
 * Скелет LDAP‑адаптера. Работает, только если установлен ext-ldap.
 * Конфиг:
 * [
 *   "host":"ldaps://ldap.example",
 *   "bindDn":"cn=reader,ou=svc,dc=example,dc=com",
 *   "bindPass":"***",
 *   "baseDn":"ou=users,dc=example,dc=com",
 *   "userFilter":"(uid={uid})",
 *   "groupAttr":"memberOf",
 *   "groupRoleMap": { "cn=admins,ou=groups,dc=example,dc=com": {"role":"admin","tenantId":"t1"} }
 * ]
 */
final class LdapAclSource implements AclSourceInterface
{
    /** @var array */
    private array $cfg;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->cfg = $config;
    }

    /**
     * @param \App\Entity\Role\SubjectId $subject
     * @param \App\Entity\Role\Scope $scope
     * @param array $ctx
     * @return array
     */
    public function rolesFor(SubjectId $subject, Scope $scope, array $ctx = []): array
    {
        if (!function_exists('ldap_connect')) {
            return [];
        }
        $uid = $subject->value();
        $conn = @ldap_connect((string) $this->cfg['host']);
        if (!$conn) {
            return [];
        }
        @ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
        @ldap_bind($conn, (string) $this->cfg['bindDn'], (string) $this->cfg['bindPass']);

        $filter = str_replace('{uid}', ldap_escape($uid, '', LDAP_ESCAPE_FILTER), (string) $this->cfg['userFilter']);
        $sr = @ldap_search($conn, (string) $this->cfg['baseDn'], $filter, [(string) ($this->cfg['groupAttr'] ?? 'memberOf')]);
        if (!$sr) {
            @ldap_unbind($conn);
            return [];
        }
        $entries = @ldap_get_entries($conn, $sr);
        @ldap_unbind($conn);
        if (!is_array($entries) || ($entries['count'] ?? 0) < 1) {
            return [];
        }
        $groups = [];
        $attr = (string) ($this->cfg['groupAttr'] ?? 'memberOf');
        $e = $entries[0] ?? [];
        $n = (int) ($e[$attr]['count'] ?? 0);
        for ($i = 0; $i < $n; $i++) {
            $groups[] = (string) $e[$attr][$i];
        }

        $roles = [];
        $map = (array) ($this->cfg['groupRoleMap'] ?? []);
        $scopeKey = $scope->key();
        foreach ($groups as $dn) {
            $m = (array) ($map[$dn] ?? null);
            if (!$m) {
                continue;
            }
            $role = (string) ($m['role'] ?? '');
            $tenantId = isset($m['tenantId']) ? (string) $m['tenantId'] : null;

            if ($tenantId) {
                if (!str_starts_with($scopeKey, 'tenant:') || !str_contains($scopeKey, ':' . $tenantId)) {
                    continue;
                }
            } else {
                if ($scopeKey !== 'global') {
                    continue;
                }
            }
            if ($role !== '') {
                $roles[] = $role;
            }
        }
        return array_values(array_unique($roles));
    }

    /**
     * @param string $role
     * @return array
     */
    public function permissionsForRole(string $role): array
    {
        return [];
    }
}
