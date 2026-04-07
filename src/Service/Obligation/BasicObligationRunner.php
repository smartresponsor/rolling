<?php
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Service\Obligation;

use App\ServiceInterface\Audit\AuditTrailInterface;
use App\ServiceInterface\Mask\DataMaskerInterface;
use App\ServiceInterface\Obligation\ObligationRunnerInterface;

/**
 * Apply obligations like audit:*, mask:*, redact:* over result.
 */
final class BasicObligationRunner implements ObligationRunnerInterface
{
    /**
     * @param \App\ServiceInterface\Audit\AuditTrailInterface $audit
     * @param \App\ServiceInterface\Mask\DataMaskerInterface $masker
     * @param array $config
     */
    public function __construct(
        private readonly AuditTrailInterface $audit,
        private readonly DataMaskerInterface $masker,
        /** @var array<string,mixed> */
        private readonly array               $config = [
            // map obligation → behavior
            // e.g. 'mask.email' => ['mask' => ['email' => 'redact']]
        ]
    )
    {
    }

    /**
     * @param array $decision
     * @param array $subject
     * @param array $resource
     * @return array
     */
    public function apply(array $decision, array $subject, array $resource): array
    {
        $effects = [];
        $obls = (array)($decision['obligations'] ?? []);
        foreach ($obls as $obl) {
            if (str_starts_with($obl, 'audit')) {
                $this->audit->write([
                    'kind' => 'obligation',
                    'obl' => $obl,
                    'subject' => $subject['id'] ?? null,
                    'action' => $decision['action'] ?? null,
                    'resource' => $resource['id'] ?? null,
                    'result' => $decision['allowed'] ?? null,
                ]);
                $effects[] = "audit:" . $obl;
                continue;
            }
            if (str_starts_with($obl, 'mask.')) {
                $rule = substr($obl, 5); // e.g. "email:redact" or "phone:last4"
                [$field, $mode] = array_pad(explode(':', $rule, 2), 2, 'redact');
                $resource = $this->masker->mask($resource, [$field => $mode]);
                $effects[] = "mask:" . $field . ":" . $mode;
                continue;
            }
            if (str_starts_with($obl, 'redact.')) {
                $field = substr($obl, 7);
                $resource = $this->masker->mask($resource, [$field => 'redact']);
                $effects[] = "redact:" . $field;
            }
        }

        $decision['effects'] = $effects;
        return ['decision' => $decision, 'subject' => $subject, 'resource' => $resource, 'effects' => $effects];
    }
}
