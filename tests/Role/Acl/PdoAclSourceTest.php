<?php

declare(strict_types=1);

namespace Tests\Role\Acl;

use App\Rolling\Entity\Role\Scope;
use App\Rolling\Entity\Role\SubjectId;
use App\Rolling\Infrastructure\Acl\Source\PdoAclSource;
use PHPUnit\Framework\TestCase;

final class PdoAclSourceTest extends TestCase
{
    /**
     * @return void
     */
    public function testRolesAndPerms(): void
    {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('pdo_sqlite is not available in the local PHP CLI.');
        }

        $pdo = new \PDO('sqlite::memory:');
        $pdo->exec('CREATE TABLE role_bindings(subject_id TEXT, role TEXT, scope_type TEXT, tenant_id TEXT, resource_id TEXT)');
        $pdo->exec('CREATE TABLE role_permissions(role TEXT, permission TEXT)');
        $pdo->exec("INSERT INTO role_bindings VALUES ('u1','admin','global',NULL,NULL)");
        $pdo->exec("INSERT INTO role_bindings VALUES ('u1','reader','tenant','t1',NULL)");
        $pdo->exec("INSERT INTO role_permissions VALUES ('admin','*')");
        $pdo->exec("INSERT INTO role_permissions VALUES ('reader','message.read')");

        $src = new PdoAclSource($pdo);
        $rolesGlobal = $src->rolesFor(new SubjectId('u1'), Scope::global());
        $rolesTenant = $src->rolesFor(new SubjectId('u1'), Scope::tenant('t1'));
        $this->assertContains('admin', $rolesGlobal);
        $this->assertContains('reader', $rolesTenant);

        $perms = $src->permissionsForRole('reader');
        $this->assertContains('message.read', $perms);
    }
}
