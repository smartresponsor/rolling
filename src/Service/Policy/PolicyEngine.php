<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\Service\Policy;

use App\Rolling\ServiceInterface\Policy\PolicyEngineInterface;
use App\Rolling\ServiceInterface\Policy\VoterInterface;

final class PolicyEngine implements PolicyEngineInterface
{
    /** @var VoterInterface[] */
    private array $voters = [];

    /**
     * @param string $strategy
     */
    public function __construct(private readonly string $strategy = 'affirmative')
    {
    }

    /**
     * @return string
     */
    public function getStrategy(): string
    {
        return $this->strategy;
    }

    /**
     * @param VoterInterface $voter
     *
     * @return void
     */
    public function addVoter(VoterInterface $voter): void
    {
        $this->voters[$voter->id()] = $voter;
    }

    /**
     * @return array|VoterInterface[]
     */
    public function getVoters(): array
    {
        return array_values($this->voters);
    }

    /**
     * @param array  $subject
     * @param string $action
     * @param array  $resource
     * @param array  $context
     *
     * @return Decision
     */
    public function decide(array $subject, string $action, array $resource, array $context = []): Decision
    {
        $grants = 0;
        $denies = 0;
        $abstains = 0;
        $trace = [];

        foreach ($this->voters as $id => $voter) {
            $res = $voter->vote($subject, $action, $resource, $context);
            $trace[] = ['voter' => $id, 'result' => $res];
            if (VoterInterface::GRANT === $res) {
                ++$grants;
            } elseif (VoterInterface::DENY === $res) {
                ++$denies;
            } else {
                ++$abstains;
            }
        }

        $strategy = $this->strategy;
        $meta = ['strategy' => $strategy, 'grants' => $grants, 'denies' => $denies, 'abstains' => $abstains, 'trace' => $trace];

        switch ($strategy) {
            case 'unanimous':
                // All non-abstain must GRANT, and no DENY.
                if ($denies > 0) {
                    return Decision::deny($meta);
                }
                if ($grants > 0 && 0 === $denies) {
                    $nonAbstain = $grants + $denies;
                    if ($nonAbstain === $grants) {
                        return Decision::allow($meta);
                    }
                }

                return Decision::deny($meta);

            case 'consensus':
                // Majority of non-abstain voters must GRANT; no hard deny dominance.
                if ($grants > $denies) {
                    return Decision::allow($meta);
                }

                return Decision::deny($meta);

            case 'affirmative':
            default:
                // Any GRANT wins unless there is an explicit DENY-voter policy preference.
                // Here: if any GRANT and no DENY -> allow. If both present, prefer DENY.
                if ($grants > 0 && 0 === $denies) {
                    return Decision::allow($meta);
                }
                if ($denies > 0) {
                    return Decision::deny($meta);
                }

                return Decision::deny($meta);
        }
    }
}
