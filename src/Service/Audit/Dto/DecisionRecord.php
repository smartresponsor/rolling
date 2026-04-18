<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Service\Audit\Dto;

/**
 *
 */

/**
 *
 */
final class DecisionRecord
{
    /**
     * @param string $id
     * @param \App\Service\Audit\Dto\DecisionInput $input
     * @param \App\Service\Audit\Dto\DecisionResult $result
     * @param array $explain
     * @param int $ts
     * @param string|null $tenant
     * @param string|null $requestor
     * @param string|null $correlationId
     */
    public function __construct(
        public string         $id,
        public DecisionInput  $input,
        public DecisionResult $result,
        /** @var array<string,mixed> */
        public array          $explain = [],
        public int            $ts = 0,
        public ?string        $tenant = null,
        public ?string        $requestor = null,
        public ?string        $correlationId = null,
    ) {
        $this->ts = $this->ts ?: time();
        $this->tenant = $this->tenant ?? ($input->context['tenant'] ?? ($input->resource['tenant'] ?? ($input->subject['tenant'] ?? null)));
        $this->requestor = $this->requestor ?? ($input->subject['id'] ?? null);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'ts' => $this->ts,
            'tenant' => $this->tenant,
            'requestor' => $this->requestor,
            'correlationId' => $this->correlationId,
            'input' => [
                'subject' => $this->input->subject,
                'action' => $this->input->action,
                'resource' => $this->input->resource,
                'context' => $this->input->context,
                'voterTrace' => $this->input->voterTrace,
            ],
            'result' => [
                'allow' => $this->result->allow,
                'policyVersion' => $this->result->policyVersion,
                'ruleId' => $this->result->ruleId,
                'obligations' => $this->result->obligations,
                'meta' => $this->result->meta,
            ],
            'explain' => $this->explain,
        ];
    }
}
