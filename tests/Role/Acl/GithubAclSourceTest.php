<?php

declare(strict_types=1);

namespace Tests\Role\Acl;

<<<<<<< HEAD
use App\Infrastructure\Acl\Source\GithubAclSource;
use App\Infrastructure\Acl\Source\GithubSubjectResolver;
use App\Net\Http\SimpleHttpClientInterface;
use PHPUnit\Framework\TestCase;
use App\Entity\Role\Scope;
use App\Entity\Role\SubjectId;
=======
use App\Acl\Role\Adapter\GithubAclSource;
use App\Acl\Role\Adapter\GithubSubjectResolver;
use App\Net\Http\SimpleHttpClientInterface;
use PHPUnit\Framework\TestCase;
use src\Entity\Role\Scope;
use src\Entity\Role\SubjectId;
>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa

final class GithubAclSourceTest extends TestCase
{
    public function testMapsTeamToRole(): void
    {
<<<<<<< HEAD
        $http = new class ([
            'https://api.github.com/orgs/acme/teams/admins/memberships/u1' => 200,
        ]) implements SimpleHttpClientInterface {
            public function __construct(private readonly array $urls)
            {
            }

            public function request(string $method, string $url, array $headers = [], ?string $body = null, int $timeoutMs = 5000): array
            {
                $status = $this->urls[$url] ?? 404;

=======
        $http = new class implements SimpleHttpClientInterface {
            public function request(string $method, string $url, array $headers = [], ?string $body = null, int $timeoutMs = 3000): array
            {
                $status = $url === 'https://api.github.com/orgs/acme/teams/admins/memberships/u1' ? 200 : 404;

>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa
                return [
                    'status' => $status,
                    'headers' => [],
                    'body' => $status === 200 ? json_encode(['state' => 'active']) : '{}',
                ];
            }
        };
<<<<<<< HEAD

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
=======
        $resolver = new class implements GithubSubjectResolver {
            public function githubLogin(SubjectId $s): ?string
            {
                return $s->value();
            }
        };

        $cfg = ['org' => 'acme', 'mappings' => [['team' => 'admins', 'role' => 'admin', 'tenantId' => 't1']]];
>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa
        $src = new GithubAclSource($http, $cfg, $resolver);

        $roles = $src->rolesFor(new SubjectId('u1'), Scope::tenant('t1'));
        $this->assertContains('admin', $roles);
        $this->assertSame([], $src->permissionsForRole('admin'));
    }
}
