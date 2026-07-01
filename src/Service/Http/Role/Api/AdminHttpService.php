<?php

declare(strict_types=1);

namespace App\Rolling\Service\Http\Role\Api;

use App\Rolling\DTO\Http\Role\Admin\AdminApprovalDecisionPayload;
use App\Rolling\DTO\Http\Role\Admin\AdminApprovalStartPayload;
use App\Rolling\DTO\Http\Role\Admin\AdminDelegationPayload;
use App\Rolling\Infrastructure\Admin\ApprovalFsStore;
use App\Rolling\Infrastructure\Admin\ApproverFsDirectory;
use App\Rolling\Infrastructure\Admin\OverrideFsPolicy;
use App\Rolling\Service\Admin\AdminRelationApprovalWorkflowService;
use App\Rolling\Service\Http\Request\JsonPayloadReader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class AdminHttpService
{
    public function __construct(
        private readonly JsonPayloadReader $payloadReader,
        private readonly string $baseDir = __DIR__.'/../../../../var',
    ) {
    }

    private function svc(): AdminRelationApprovalWorkflowService
    {
        return new AdminRelationApprovalWorkflowService(
            new ApprovalFsStore($this->baseDir.'/admin'),
            new ApproverFsDirectory($this->baseDir.'/admin'),
            new OverrideFsPolicy($this->baseDir.'/admin'),
        );
    }

    public function start(Request $req): JsonResponse
    {
        $payload = AdminApprovalStartPayload::fromArray($this->payloadReader->readObject($req));
        $out = $this->svc()->start(
            $payload->tenant,
            $payload->relation,
            $payload->resource,
            $payload->requester,
            $payload->options,
        );

        return new JsonResponse($out, 200);
    }

    public function approve(Request $req): JsonResponse
    {
        $payload = AdminApprovalDecisionPayload::fromArray($this->payloadReader->readObject($req));
        $out = $this->svc()->approve($payload->id, $payload->subject, $payload->comment);

        return new JsonResponse($out, 200);
    }

    public function reject(Request $req): JsonResponse
    {
        $payload = AdminApprovalDecisionPayload::fromArray($this->payloadReader->readObject($req));
        $out = $this->svc()->reject($payload->id, $payload->subject, $payload->reason);

        return new JsonResponse($out, 200);
    }

    public function delegate(Request $req): JsonResponse
    {
        $payload = AdminDelegationPayload::fromArray($this->payloadReader->readObject($req));
        $base = $this->baseDir.'/admin';
        @mkdir($base, 0775, true);
        $file = $base.'/delegations.json';
        $j = is_file($file) ? json_decode((string) file_get_contents($file), true) : [];
        if (!is_array($j)) {
            $j = [];
        }

        $row = $payload->toRow();
        $j[$payload->tenant] = array_values(array_merge((array) ($j[$payload->tenant] ?? []), [$row]));
        file_put_contents($file, json_encode($j, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

        return new JsonResponse(['ok' => true, 'row' => $row], 200);
    }

    public function override(Request $req): JsonResponse
    {
        $payload = AdminApprovalDecisionPayload::fromArray($this->payloadReader->readObject($req));
        $out = $this->svc()->override($payload->id, $payload->actor, $payload->reason);

        return new JsonResponse($out, 200);
    }
}
