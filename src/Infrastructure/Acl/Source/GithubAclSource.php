<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Acl\Source;

use App\Rolling\Entity\Role\Scope;
use App\Rolling\Entity\Role\SubjectId;
use App\Rolling\InfrastructureInterface\Acl\AclSourceInterface;
use App\Rolling\InfrastructureInterface\Acl\Source\GithubSubjectResolverInterface;
use App\Rolling\Net\Http\SimpleHttpClientInterface;

/**
 * Maps GitHub teams to local roles from configuration.
 * Configuration:
 * [
 *   "org": "acme",
 *   "tokenEnv": "GITHUB_TOKEN",
 *   "mappings": [
 *     {"team":"admins","role":"admin","tenantId":"t1"},
 *     {"team":"support","role":"reader","tenantId":"t1"}
 *   ]
 * ].
 */
final class GithubAclSource implements AclSourceInterface
{
    private array $cfg;
    private SimpleHttpClientInterface $http;
    private GithubSubjectResolverInterface $resolver;

    public function __construct(SimpleHttpClientInterface $http, array $config, GithubSubjectResolverInterface $resolver)
    {
        $this->http = $http;
        $this->cfg = $config;
        $this->resolver = $resolver;
    }

    public function rolesFor(SubjectId $subject, Scope $scope, array $ctx = []): array
    {
        $login = $this->resolver->githubLogin($subject);
        if (!$login) {
            return [];
        }

        $roles = [];
        $mappings = (array) ($this->cfg['mappings'] ?? []);
        $org = (string) ($this->cfg['org'] ?? '');
        $token = getenv((string) ($this->cfg['tokenEnv'] ?? '')) ?: null;
        $headers = ['User-Agent' => 'SmartResponsor-Role', 'Accept' => 'application/vnd.github+json'];
        if ($token) {
            $headers['Authorization'] = 'Bearer '.$token;
        }

        $scopeKey = $scope->key();
        foreach ($mappings as $m) {
            $team = (string) ($m['team'] ?? '');
            $role = (string) ($m['role'] ?? '');
            $tenantId = isset($m['tenantId']) ? (string) $m['tenantId'] : null;
            // Scope match: global/tenant/resource. This source uses tenant/global only.
            if ($tenantId) {
                if (!str_starts_with($scopeKey, 'tenant:') || !str_contains($scopeKey, ':'.$tenantId)) {
                    continue;
                }
            } else {
                if ('global' !== $scopeKey) {
                    continue;
                }
            }

            if ($this->isMember($org, $team, $login, $headers)) {
                $roles[] = $role;
            }
        }

        return array_values(array_unique($roles));
    }

    public function permissionsForRole(string $role): array
    {
        // GitHub source does not own permissions; other sources provide them.
        return [];
    }

    private function isMember(string $org, string $team, string $login, array $headers): bool
    {
        $url = "https://api.github.com/orgs/{$org}/teams/{$team}/memberships/{$login}";
        try {
            $resp = $this->http->request('GET', $url, $headers, null, 3000);
            if (200 === $resp['status']) {
                $data = json_decode($resp['body'] ?? 'null', true);

                return is_array($data) && (($data['state'] ?? '') === 'active');
            }
            if (404 === $resp['status']) {
                return false;
            }
        } catch (\Throwable $e) {
            error_log('GithubAclSource::isMember request failure: '.$e->getMessage());
        }

        return false;
    }
}
