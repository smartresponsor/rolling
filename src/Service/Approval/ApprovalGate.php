<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Service\Approval;

use App\ServiceInterface\Approval\ApprovalGateInterface;
use App\ServiceInterface\Approval\ApprovalStoreInterface;

/**
 * Enforce SoD and four-eyes approval.
 */
final class ApprovalGate implements ApprovalGateInterface
{
    /**
     * @param \App\ServiceInterface\Approval\ApprovalStoreInterface $store
     * @param array $rule
     */
    public function __construct(
        private readonly ApprovalStoreInterface $store,
        /** @var array<string,mixed> */
        private readonly array                  $rule = [
            // sample: delete on doc demands four-eyes unless subject has role admin
            'action' => 'delete',
            'resource.type' => 'doc',
            'skipRole' => 'admin',
        ],
    ) {}

    /**
     * @param array $decision
     * @param array $subject
     * @param string $action
     * @param array $resource
     * @return array
     */
    public function gate(array $decision, array $subject, string $action, array $resource): array
    {
        $need = false;

        if (($action === ($this->rule['action'] ?? null)) && (($resource['type'] ?? null) === ($this->rule['resource.type'] ?? null))) {
            $roles = (array) ($subject['roles'] ?? []);
            if (!in_array((string) ($this->rule['skipRole'] ?? ''), array_map('strval', $roles), true)) {
                $need = true;
            }
        }

        if (($decision['allowed'] ?? false) !== true) {
            // no need to gate denied result
            return $decision;
        }

        if ($need) {
            $case = [
                'subjectId' => $subject['id'] ?? null,
                'action' => $action,
                'resource' => $resource,
                'ruleId' => $decision['ruleId'] ?? null,
                'reason' => $decision['reason'] ?? null,
            ];
            $id = $this->store->create($case);
            return [
                'allowed' => false,
                'ruleId' => $decision['ruleId'] ?? '',
                'reason' => 'four-eyes required',
                'approvalId' => $id,
                'sod' => 'pending',
            ];
        }

        return $decision;
    }

    /**
     * @param string $id
     * @return array|null
     */
    public function resolve(string $id): ?array
    {
        $rec = $this->store->read($id);
        if (!$rec) {
            return null;
        }
        if ($rec['state'] !== 'approved') {
            return null;
        }
        $case = $rec['case'] ?? [];
        return [
            'allowed' => true,
            'ruleId' => (string) ($case['ruleId'] ?? ''),
            'reason' => 'four-eyes approved',
            'approvalId' => $id,
            'sod' => 'approved',
        ];
    }
}
