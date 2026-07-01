<?php

declare(strict_types=1);
/* Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp */

namespace App\Rolling\Service\Admin;

use App\Rolling\ServiceInterface\Admin\ApprovalStoreInterface;
use App\Rolling\ServiceInterface\Admin\ApproverDirectoryInterface;
use App\Rolling\ServiceInterface\Admin\OverridePolicyInterface;

final class AdminRelationApprovalWorkflowService
{
    public function __construct(
        private readonly ApprovalStoreInterface $store,
        private readonly ApproverDirectoryInterface $dir,
        private readonly OverridePolicyInterface $ovr,
    ) {
    }

    public function start(string $tenant, string $relation, string $resource, string $requester, array $opts = []): array
    {
        $row = [
            'tenant' => $tenant, 'relation' => $relation, 'resource' => $resource, 'requester' => $requester,
            'required' => (int) ($opts['required'] ?? 2),
            'distinctBy' => (string) ($opts['distinctBy'] ?? 'subject'),
            'title' => (string) ($opts['title'] ?? ''),
        ];
        $id = $this->store->create($row);

        return ['id' => $id] + $row;
    }

    public function approve(string $id, string $subject, string $comment = ''): array
    {
        $cur = $this->need($id);
        if ('pending' !== $cur['status']) {
            return $cur;
        }
        // SoD: requester cannot approve own request
        if ($cur['requester'] === $subject) {
            throw new \RuntimeException('SOD: requester cannot approve');
        }
        // Directory/Delegation
        $tenant = (string) $cur['tenant'];
        $actor = $subject;
        if (!$this->dir->canApprove($tenant, $actor, (string) $cur['relation'], (string) $cur['resource'])) {
            $delegate = $this->dir->resolveDelegate($tenant, $subject);
            if (!$delegate || !$this->dir->canApprove($tenant, $delegate, (string) $cur['relation'], (string) $cur['resource'])) {
                throw new \RuntimeException('Not allowed to approve');
            }
            $actor = $delegate;
        }
        // Already approved by same actor?
        foreach ((array) $cur['approvals'] as $a) {
            if (($a['actor'] ?? '') === $actor) {
                return $cur;
            }
        }
        $cur['approvals'][] = ['actor' => $actor, 'by' => $subject, 'comment' => $comment, 'ts' => gmdate('c')];
        // Check N-of-M
        if (count($cur['approvals']) >= (int) $cur['required']) {
            $cur['status'] = 'approved';
        }
        $this->store->save($id, $cur);

        return $cur;
    }

    public function reject(string $id, string $subject, string $reason = ''): array
    {
        $cur = $this->need($id);
        if ('pending' !== $cur['status']) {
            return $cur;
        }
        $cur['rejections'][] = ['actor' => $subject, 'reason' => $reason, 'ts' => gmdate('c')];
        $cur['status'] = 'rejected';
        $this->store->save($id, $cur);

        return $cur;
    }

    public function override(string $id, string $actor, string $reason = ''): array
    {
        $cur = $this->need($id);
        $tenant = (string) $cur['tenant'];
        if (!$this->ovr->canOverride($tenant, $actor, (string) $cur['relation'], (string) $cur['resource'])) {
            throw new \RuntimeException('No override permission');
        }
        $cur['status'] = 'approved';
        $cur['override'] = ['actor' => $actor, 'reason' => $reason, 'ts' => gmdate('c')];
        $this->store->save($id, $cur);

        return $cur;
    }

    private function need(string $id): array
    {
        $j = $this->store->load($id);
        if (!$j) {
            throw new \RuntimeException('Approval not found: '.$id);
        }

        return $j;
    }
}
