<?php
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace Audit\Explain;

use App\ServiceInterface\Role\Audit\ExplainerInterface;
use Audit\Dto\DecisionInput;
use Audit\Dto\DecisionResult;
use Audit\Dto\ExplainNode;

/**
 *
 */

/**
 *
 */
final class RuleExplainer implements ExplainerInterface
{
    /**
     * @param \Audit\Dto\DecisionInput $in
     * @param \Audit\Dto\DecisionResult $res
     * @return array
     */
    public function explain(DecisionInput $in, DecisionResult $res): array
    {
        $root = new ExplainNode('decision', $in->action, $res->allow, [
            'policyVersion' => $res->policyVersion,
            'tenant' => $in->context['tenant'] ?? null,
            'resourceType' => $in->resource['type'] ?? null,
        ]);

        // Voter trace aggregation
        $group = new ExplainNode('voters', 'Voters', true);
        $allPass = true;
        foreach ($in->voterTrace as $i => $v) {
            $pass = (bool)($v['allow'] ?? false);
            $node = new ExplainNode('voter', (string)($v['name'] ?? ('v' . $i)), $pass, [
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
            $root->add(new ExplainNode('rule', 'Matched Rule', true, ['ruleId' => $res->ruleId]));
        }
        if (!empty($res->obligations)) {
            $root->add(new ExplainNode('obligations', 'Obligations', true, $res->obligations));
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
