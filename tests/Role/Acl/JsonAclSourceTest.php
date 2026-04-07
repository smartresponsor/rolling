<?php
declare(strict_types=1);

namespace Tests\Role\Acl;

use App\Infrastructure\Acl\Source\JsonAclSource;
use PHPUnit\Framework\TestCase;
use App\Entity\Role\Scope;
use App\Entity\Role\SubjectId;

/**
 *
 */

/**
 *
 */
final class JsonAclSourceTest extends TestCase
{
    /**
     * @return void
     */
    public function testJsonBindings(): void
    {
        $cfg = [
            'roles' => ['admin' => ['*'], 'reader' => ['message.read']],
            'bindings' => [
                ['subjectId' => 'u1', 'role' => 'admin', 'scope' => 'global'],
                ['subjectId' => 'u2', 'role' => 'reader', 'scope' => 'tenant:t1'],
            ],
        ];
        $src = new JsonAclSource($cfg);
        $this->assertContains('admin', $src->rolesFor(new SubjectId('u1'), Scope::global()));
        $this->assertContains('reader', $src->rolesFor(new SubjectId('u2'), Scope::tenant('t1')));
        $this->assertContains('*', $src->permissionsForRole('admin'));
    }
}
