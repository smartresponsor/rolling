<?php
declare(strict_types=1);

namespace Tests\Role\Acl;

use App\Acl\Role\Adapter\GithubAclSource;
use App\Acl\Role\Adapter\GithubSubjectResolver;
use PHPUnit\Framework\TestCase;
use src\Entity\Role\{Scope};
use src\Entity\Role\SubjectId;

/**
 *
 */

/**
 *
 */
final class GithubAclSourceTest extends TestCase
{
final private class DummyHttp implements \App\Net\Http\SimpleHttpClientInterface
{
    /**
     * @param array $urls
     */
    public function __construct(private readonly array $urls)
    {
    }

    /**
     * @param string $url
     * @return array
     */
    public function request(string $url): array
    {
        $status = $this->urls[$url] ?? 404;
        return ['status' => $status, 'headers' => [], 'body' => $status === 200 ? json_encode(['state' => 'active']) : '{}'];
    }
}

private

/**
 *
 */

/**
 *
 */
final class IdResolver implements GithubSubjectResolver
{
    /**
     * @param \src\Entity\Role\SubjectId $s
     * @return string|null
     */
    public function githubLogin(SubjectId $s): ?string
    {
        return $s->value();
    }
}

public
/**
 * @return void
 */
function testMapsTeamToRole(): void
{
    $http = new self::DummyHttp([
        'https://api.github.com/orgs/acme/teams/admins/memberships/u1' => 200,
    ]);
    $cfg = ['org' => 'acme', 'mappings' => [['team' => 'admins', 'role' => 'admin', 'tenantId' => 't1']]];
    $src = new GithubAclSource($http, $cfg, new self::IdResolver());

    $roles = $src->rolesFor(new SubjectId('u1'), Scope::tenant('t1'));
    $this->assertContains('admin', $roles);
    $this->assertSame([], $src->permissionsForRole('admin')); // delegated to other sources
}
}
