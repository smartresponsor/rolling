<?php

declare(strict_types=1);

namespace Tests\Role\Acl;

use App\Rolling\Entity\Role\Scope;
use App\Rolling\Entity\Role\SubjectId;
use App\Rolling\Infrastructure\Acl\CachedAclSource;
use App\Rolling\Infrastructure\Acl\ChainAclSource;
use App\Rolling\InfrastructureInterface\Acl\AclSourceInterface;
use PHPUnit\Framework\TestCase;

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
