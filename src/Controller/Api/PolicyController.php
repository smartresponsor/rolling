<?php

declare(strict_types=1);

namespace App\Rolling\Controller\Api;

use App\Rolling\Infrastructure\Policy\PolicyFsStore;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class PolicyController
{
    /**
     * @return PolicyFsStore
     */
    private function store(): PolicyFsStore
    {
        return new PolicyFsStore(__DIR__.'/../../../../var/policy');
    }

    /**
     * @param Request $r
     *
     * @return JsonResponse
     */
    public function getDraft(Request $r): JsonResponse
    {
        $t = (string) ($r->query->get('tenant') ?? 't1');

        return new JsonResponse(['draft' => $this->store()->getDraft($t)], 200);
    }

    /**
     * @param Request $r
     *
     * @return JsonResponse
     */
    public function putDraft(Request $r): JsonResponse
    {
        $p = json_decode((string) $r->getContent(), true) ?? [];
        $t = (string) ($p['tenant'] ?? 't1');
        $e = (string) ($p['expr'] ?? '');
        $this->store()->putDraft($t, $e);

        return new JsonResponse(['ok' => true], 200);
    }

    /**
     * @param Request $r
     *
     * @return JsonResponse
     */
    public function publish(Request $r): JsonResponse
    {
        $p = json_decode((string) $r->getContent(), true) ?? [];
        $t = (string) ($p['tenant'] ?? 't1');
        $n = (string) ($p['note'] ?? '');
        $v = $this->store()->publish($t, $n);

        return new JsonResponse(['version' => $v], 200);
    }

    /**
     * @param Request $r
     *
     * @return JsonResponse
     */
    public function getEffective(Request $r): JsonResponse
    {
        $t = (string) ($r->query->get('tenant') ?? 't1');

        return new JsonResponse(['expr' => $this->store()->getEffective($t)], 200);
    }
}
