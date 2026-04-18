<?php

declare(strict_types=1);

namespace Tests\Role\Acl;

<<<<<<< HEAD
use App\InfrastructureInterface\Acl\AclSourceInterface;
use App\Infrastructure\Acl\CachedAclSource;
use App\Infrastructure\Acl\ChainAclSource;
use PHPUnit\Framework\TestCase;
use App\Entity\Role\Scope;
use App\Entity\Role\SubjectId;
=======
use App\Acl\Role\AclSourceInterface;
use App\Acl\Role\{CachedAclSource, ChainAclSource};
use PHPUnit\Framework\TestCase;
use src\Entity\Role\Scope;
use src\Entity\Role\SubjectId;
>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa

final class ChainAndCacheTest extends TestCase
{
    public function testChainMergesAndCacheWorks(): void
    {
<<<<<<< HEAD
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

=======
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
>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa
        $roles = $chain->rolesFor(new SubjectId('u'), Scope::global());
        $this->assertSame(['a', 'b'], $roles);

        $cached = new CachedAclSource($chain, 3600);
        $r1 = $cached->rolesFor(new SubjectId('u'), Scope::global());
        $r2 = $cached->rolesFor(new SubjectId('u'), Scope::global());
<<<<<<< HEAD

=======
>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa
        $this->assertSame($r1, $r2);
    }
}
