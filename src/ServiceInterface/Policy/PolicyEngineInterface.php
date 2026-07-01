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

    public function addVoter(VoterInterface $voter): void;

    /**
     * @return VoterInterface[]
     */
    public function getVoters(): array;

    public function decide(array $subject, string $action, array $resource, array $context = []): PolicyEngineDecision;
}
