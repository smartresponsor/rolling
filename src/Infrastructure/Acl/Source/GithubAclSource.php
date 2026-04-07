<?php
declare(strict_types=1);

namespace App\Infrastructure\Acl\Source;

use App\InfrastructureInterface\Acl\AclSourceInterface;
use App\Net\Http\SimpleHttpClientInterface;
use App\Entity\Role\Scope;
use App\Entity\Role\SubjectId;
use Throwable;

/**
 * Мапит GitHub teams -> локальные роли по конфигу.
 * Конфиг:
 * [
 *   "org": "acme",
 *   "tokenEnv": "GITHUB_TOKEN",
 *   "mappings": [
 *     {"team":"admins","role":"admin","tenantId":"t1"},
 *     {"team":"support","role":"reader","tenantId":"t1"}
 *   ]
 * ]
 */
final class GithubAclSource implements AclSourceInterface
{
    /** @var array */
    private array $cfg;
    private SimpleHttpClientInterface $http;
    private GithubSubjectResolver $resolver;

    /**
     * @param \App\Net\Http\SimpleHttpClientInterface $http
     * @param array $config
     * @param \App\Legacy\Acl\Source\GithubSubjectResolver|null $resolver
     */
    public function __construct(SimpleHttpClientInterface $http, array $config, ?GithubSubjectResolver $resolver = null)
    {
        $this->http = $http;
        $this->cfg = $config;
        $this->resolver = $resolver ?? new DefaultGithubResolver();
    }

    /**
     * @param \App\Entity\Role\SubjectId $subject
     * @param \App\Entity\Role\Scope $scope
     * @param array $ctx
     * @return array
     */
    public function rolesFor(SubjectId $subject, Scope $scope, array $ctx = []): array
    {
        $login = $this->resolver->githubLogin($subject);
        if (!$login) return [];

        $roles = [];
        $mappings = (array)($this->cfg['mappings'] ?? []);
        $org = (string)($this->cfg['org'] ?? '');
        $token = getenv((string)($this->cfg['tokenEnv'] ?? '')) ?: null;
        $headers = ['User-Agent' => 'SmartResponsor-Role', 'Accept' => 'application/vnd.github+json'];
        if ($token) $headers['Authorization'] = 'Bearer ' . $token;

        $scopeKey = $scope->key();
        foreach ($mappings as $m) {
            $team = (string)($m['team'] ?? '');
            $role = (string)($m['role'] ?? '');
            $tenantId = isset($m['tenantId']) ? (string)$m['tenantId'] : null;
            // Соответствие scope: global/tenant/resource (тут используем только tenant/global)
            if ($tenantId) {
                if (!str_starts_with($scopeKey, 'tenant:') || !str_contains($scopeKey, ':' . $tenantId)) {
                    continue;
                }
            } else {
                if ($scopeKey !== 'global') continue;
            }

            if ($this->isMember($org, $team, $login, $headers)) {
                $roles[] = $role;
            }
        }
        return array_values(array_unique($roles));
    }

    /**
     * @param string $role
     * @return array
     */
    public function permissionsForRole(string $role): array
    {
        // GitHub источник не хранит permissions — оставляем пустым, их дадут другие источники (Json/PDO).
        return [];
    }

    /**
     * @param string $org
     * @param string $team
     * @param string $login
     * @param array $headers
     * @return bool
     */
    private function isMember(string $org, string $team, string $login, array $headers): bool
    {
        $url = "https://api.github.com/orgs/{$org}/teams/{$team}/memberships/{$login}";
        try {
            $resp = $this->http->request('GET', $url, $headers, null, 3000);
            if ($resp['status'] === 200) {
                $data = json_decode($resp['body'] ?? 'null', true);
                return is_array($data) && (($data['state'] ?? '') === 'active');
            }
            if ($resp['status'] === 404) return false;
        } catch (Throwable $e) {
            error_log('GithubAclSource::isMember request failure: ' . $e->getMessage());
        }
        return false;
    }
}
