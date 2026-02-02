<?php
declare(strict_types=1);

namespace Tests\Role\Acl;

use App\Acl\Role\{CachedAclSource, ChainAclSource};
use PHPUnit\Framework\TestCase;
use src\Entity\Role\{Scope};
use src\Entity\Role\SubjectId;

/**
 *
 */

/**
 *
 */
final class ChainAndCacheTest extends TestCase
{
final private class Stub implements AclSourceInterface
{
    /**
     * @return string[]
     */
    public function rolesFor(): array
    {
        return ['a', 'b'];
    }

    /**
     * @return string[]
     */
    public function permissionsForRole(): array
    {
        return ['x'];
    }
}

public
/**
 * @return void
 */
function testChainMergesAndCacheWorks(): void
{
    $chain = new ChainAclSource([new self::Stub(), new self::Stub()]);
    $roles = $chain->rolesFor(new SubjectId('u'), Scope::global());
    $this->assertSame(['a', 'b'], $roles);

    $cached = new CachedAclSource($chain, 3600);
    $r1 = $cached->rolesFor(new SubjectId('u'), Scope::global());
    $r2 = $cached->rolesFor(new SubjectId('u'), Scope::global());
    $this->assertSame($r1, $r2);
}
}
