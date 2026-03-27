<?php

declare(strict_types=1);

namespace Tests\Role\Acl;

use App\Acl\Role\Adapter\GithubAclSource;
use App\Acl\Role\Adapter\GithubSubjectResolver;
use App\Net\Http\SimpleHttpClientInterface;
use PHPUnit\Framework\TestCase;
use src\Entity\Role\Scope;
use src\Entity\Role\SubjectId;

/**
 *
 */

/**
 *
 */
final class GithubAclSourceTest extends TestCase
{
    public function testMapsTeamToRole(): void
    {
        $http = new class implements SimpleHttpClientInterface {
            public function request(string $method, string $url, array $headers = [], ?string $body = null, int $timeoutMs = 3000): array
            {
                $status = $url === 'https://api.github.com/orgs/acme/teams/admins/memberships/u1' ? 200 : 404;

                return [
                    'status' => $status,
                    'headers' => [],
                    'body' => $status === 200 ? json_encode(['state' => 'active']) : '{}',
                ];
            }
        };
        $resolver = new class implements GithubSubjectResolver {
            public function githubLogin(SubjectId $s): ?string
            {
                return $s->value();
            }
        };

        $cfg = ['org' => 'acme', 'mappings' => [['team' => 'admins', 'role' => 'admin', 'tenantId' => 't1']]];
        $src = new GithubAclSource($http, $cfg, $resolver);

        $roles = $src->rolesFor(new SubjectId('u1'), Scope::tenant('t1'));
        $this->assertContains('admin', $roles);
        $this->assertSame([], $src->permissionsForRole('admin'));
    }
}
