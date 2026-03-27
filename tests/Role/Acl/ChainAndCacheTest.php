<?php

declare(strict_types=1);

namespace Tests\Role\Acl;

use App\Acl\Role\AclSourceInterface;
use App\Acl\Role\{CachedAclSource, ChainAclSource};
use PHPUnit\Framework\TestCase;
use src\Entity\Role\Scope;
use src\Entity\Role\SubjectId;

/**
 *
 */

/**
 *
 */
final class ChainAndCacheTest extends TestCase
{
    public function testChainMergesAndCacheWorks(): void
    {
        $stub = new class implements AclSourceInterface {
            public function rolesFor(SubjectId $subject, Scope $scope, array $ctx = []): array
            {
                return ['a', 'b'];
            }

            public function permissionsForRole(string $role): array
            {
                return ['x'];
            }
        };

        $chain = new ChainAclSource([$stub, $stub]);
        $roles = $chain->rolesFor(new SubjectId('u'), Scope::global());
        $this->assertSame(['a', 'b'], $roles);

        $cached = new CachedAclSource($chain, 3600);
        $r1 = $cached->rolesFor(new SubjectId('u'), Scope::global());
        $r2 = $cached->rolesFor(new SubjectId('u'), Scope::global());
        $this->assertSame($r1, $r2);
    }
}
