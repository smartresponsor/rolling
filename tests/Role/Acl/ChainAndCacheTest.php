<?php

declare(strict_types=1);

namespace Tests\Role\Acl;

use App\InfrastructureInterface\Acl\AclSourceInterface;
use App\Infrastructure\Acl\CachedAclSource;
use App\Infrastructure\Acl\ChainAclSource;
use PHPUnit\Framework\TestCase;
use App\Entity\Role\Scope;
use App\Entity\Role\SubjectId;

final class ChainAndCacheTest extends TestCase
{
    public function testChainMergesAndCacheWorks(): void
    {
        $chain = new ChainAclSource([
            new class implements AclSourceInterface {
                public function rolesFor(SubjectId $subjectId, Scope $scope, array $ctx = []): array
                {
                    return ['a', 'b'];
                }

                public function permissionsForRole(string $role): array
                {
                    return ['x'];
                }
            },
            new class implements AclSourceInterface {
                public function rolesFor(SubjectId $subjectId, Scope $scope, array $ctx = []): array
                {
                    return ['a', 'b'];
                }

                public function permissionsForRole(string $role): array
                {
                    return ['x'];
                }
            },
        ]);

        $roles = $chain->rolesFor(new SubjectId('u'), Scope::global());
        $this->assertSame(['a', 'b'], $roles);

        $cached = new CachedAclSource($chain, 3600);
        $r1 = $cached->rolesFor(new SubjectId('u'), Scope::global());
        $r2 = $cached->rolesFor(new SubjectId('u'), Scope::global());

        $this->assertSame($r1, $r2);
    }
}
