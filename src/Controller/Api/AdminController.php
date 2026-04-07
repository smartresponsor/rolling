<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Service\Admin\AdminWorkflowService;
use App\Infrastructure\Admin\{ApprovalFsStore, ApproverFsDirectory, OverrideFsPolicy};
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 */

/**
 *
 */
final class AdminController
{
    /**
     * @param string $baseDir
     */
    public function __construct(private readonly string $baseDir = __DIR__ . '/../../../../var')
    {
    }

    /**
     * @return \Admin\AdminWorkflowService
     */
    private function svc(): AdminWorkflowService
    {
        return new AdminWorkflowService(
            new ApprovalFsStore($this->baseDir . '/admin'),
            new ApproverFsDirectory($this->baseDir . '/admin'),
            new OverrideFsPolicy($this->baseDir . '/admin')
        );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $req
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function start(Request $req): JsonResponse
    {
        $p = json_decode((string)$req->getContent(), true) ?? [];
        $out = $this->svc()->start(
            (string)($p['tenant'] ?? 't1'),
            (string)($p['relation'] ?? 'change-policy'),
            (string)($p['resource'] ?? 'policy:active'),
            (string)($p['requester'] ?? 'user:unknown'),
            (array)($p['opts'] ?? [])
        );
        return new JsonResponse($out, 200);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $req
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function approve(Request $req): JsonResponse
    {
        $p = json_decode((string)$req->getContent(), true) ?? [];
        $out = $this->svc()->approve(
            (string)($p['id'] ?? ''),
            (string)($p['subject'] ?? ''),
            (string)($p['comment'] ?? '')
        );
        return new JsonResponse($out, 200);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $req
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function reject(Request $req): JsonResponse
    {
        $p = json_decode((string)$req->getContent(), true) ?? [];
        $out = $this->svc()->reject(
            (string)($p['id'] ?? ''),
            (string)($p['subject'] ?? ''),
            (string)($p['reason'] ?? '')
        );
        return new JsonResponse($out, 200);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $req
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function delegate(Request $req): JsonResponse
    {
        $p = json_decode((string)$req->getContent(), true) ?? [];
        // write delegation file
        $base = $this->baseDir . '/admin';
        @mkdir($base, 0775, true);
        $file = $base . '/delegations.json';
        $j = is_file($file) ? json_decode((string)file_get_contents($file), true) : [];
        if (!is_array($j)) $j = [];
        $tenant = (string)($p['tenant'] ?? 't1');
        $row = [
            'from' => (string)($p['from'] ?? ''),
            'to' => (string)($p['to'] ?? ''),
            'until' => (int)($p['until'] ?? (time() + 3600)),
            'scope' => (string)($p['scope'] ?? '*'),
        ];
        $j[$tenant] = array_values(array_merge((array)($j[$tenant] ?? []), [$row]));
        file_put_contents($file, json_encode($j, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        return new JsonResponse(['ok' => true, 'row' => $row], 200);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $req
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function override(Request $req): JsonResponse
    {
        $p = json_decode((string)$req->getContent(), true) ?? [];
        $out = $this->svc()->override(
            (string)($p['id'] ?? ''),
            (string)($p['actor'] ?? ''),
            (string)($p['reason'] ?? '')
        );
        return new JsonResponse($out, 200);
    }
}
