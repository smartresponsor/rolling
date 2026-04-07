<?php
declare(strict_types=1);

namespace Tests\Role\Acl;

use App\Infrastructure\Acl\Source\GithubAclSource;
use App\Infrastructure\Acl\Source\GithubSubjectResolver;
use App\Net\Http\SimpleHttpClientInterface;
use PHPUnit\Framework\TestCase;
use App\Entity\Role\Scope;
use App\Entity\Role\SubjectId;

final class GithubAclSourceTest extends TestCase
{
    public function testMapsTeamToRole(): void
    {
        $http = new class ([
            'https://api.github.com/orgs/acme/teams/admins/memberships/u1' => 200,
        ]) implements SimpleHttpClientInterface {
            public function __construct(private readonly array $urls)
            {
            }

            public function request(string $method, string $url, array $headers = [], ?string $body = null, int $timeoutMs = 5000): array
            {
                $status = $this->urls[$url] ?? 404;

                return [
                    'status' => $status,
                    'headers' => [],
                    'body' => $status === 200 ? json_encode(['state' => 'active']) : '{}',
                ];
            }
        };

        $resolver = new class implements GithubSubjectResolver {
            public function githubLogin(SubjectId $subject): ?string
            {
                return $subject->value();
            }
        };

        $cfg = [
            'org' => 'acme',
            'mappings' => [['team' => 'admins', 'role' => 'admin', 'tenantId' => 't1']],
        ];
        $src = new GithubAclSource($http, $cfg, $resolver);

        $roles = $src->rolesFor(new SubjectId('u1'), Scope::tenant('t1'));
        $this->assertContains('admin', $roles);
        $this->assertSame([], $src->permissionsForRole('admin'));
    }
}
