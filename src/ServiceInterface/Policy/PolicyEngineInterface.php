<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Policy;

use App\Rolling\Service\Policy\PolicyEngineDecision;

interface PolicyEngineInterface
{
    /**
     * Decision strategy: "affirmative" | "consensus" | "unanimous".
     */
    public function getStrategy(): string;

    /**
     * @param VoterInterface $voter
     *
     * @return void
     */
    public function addVoter(VoterInterface $voter): void;

    /**
     * @return VoterInterface[]
     */
    public function getVoters(): array;

    /**
     * @param array  $subject
     * @param string $action
     * @param array  $resource
     * @param array  $context
     *
     * @return PolicyEngineDecision
     */
    public function decide(array $subject, string $action, array $resource, array $context = []): PolicyEngineDecision;
}
