<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\Service\Audit\Explain;

use App\Rolling\Service\Audit\Dto\AuditDecisionInputDto;
use App\Rolling\Service\Audit\Dto\AuditDecisionResultDto;
use App\Rolling\Service\Audit\Dto\AuditExplainNodeDto;
use App\Rolling\ServiceInterface\Audit\ExplainerInterface;

final class RuleExplainer implements ExplainerInterface
{
    public function explain(AuditDecisionInputDto $in, AuditDecisionResultDto $res): array
    {
        $root = new AuditExplainNodeDto('decision', $in->action, $res->allow, [
            'policyVersion' => $res->policyVersion,
            'tenant' => $in->context['tenant'] ?? null,
            'resourceType' => $in->resource['type'] ?? null,
        ]);

        // Voter trace aggregation
        $group = new AuditExplainNodeDto('voters', 'Voters', true);
        $allPass = true;
        foreach ($in->voterTrace as $i => $v) {
            $pass = (bool) ($v['allow'] ?? false);
            $node = new AuditExplainNodeDto('voter', (string) ($v['nameEntity'] ?? ('v'.$i)), $pass, [
                'reason' => $v['reason'] ?? null,
                'ruleId' => $v['ruleId'] ?? null,
                'weight' => $v['weight'] ?? 1,
                'evidence' => $v['evidence'] ?? [],
            ]);
            $group->add($node);
            $allPass = $allPass && $pass;
        }
        $group->pass = $allPass;
        $root->add($group);

        // Winning rule / obligations
        if ($res->ruleId) {
            $root->add(new AuditExplainNodeDto('rule', 'Matched Rule', true, ['ruleId' => $res->ruleId]));
        }
        if (!empty($res->obligations)) {
            $root->add(new AuditExplainNodeDto('obligations', 'Obligations', true, $res->obligations));
        }

        // Summary
        $summary = [
            'allow' => $res->allow,
            'matchedRule' => $res->ruleId,
            'votersPass' => $allPass,
            'obligationCount' => count($res->obligations),
        ];

        return ['summary' => $summary, 'tree' => $root->toArray()];
    }
}
