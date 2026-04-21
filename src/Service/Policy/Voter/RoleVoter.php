<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\Service\Policy\Voter;

use App\Rolling\InfrastructureInterface\Policy\GrantRepositoryInterface;
use App\Rolling\ServiceInterface\Policy\VoterInterface;

final class RoleVoter implements VoterInterface
{
    /**
     * @param GrantRepositoryInterface $repo
     */
    public function __construct(private readonly GrantRepositoryInterface $repo)
    {
    }

    /**
     * @return string
     */
    public function id(): string
    {
        return 'role';
    }

    /**
     * @param array  $subject
     * @param string $action
     * @param array  $resource
     * @param array  $context
     *
     * @return int
     */
    public function vote(array $subject, string $action, array $resource, array $context = []): int
    {
        $tenant = $context['tenant'] ?? null;
        $rtype = (string) ($resource['type'] ?? 'object');
        $rules = $this->repo->findGrants($rtype, $action, $tenant);
        if (!$rules) {
            return self::ABSTAIN;
        }

        $roles = $subject['roles'] ?? [];
        foreach ($rules as $r) {
            if (isset($r['role']) && in_array($r['role'], $roles, true)) {
                return self::GRANT;
            }
            if (isset($r['user']) && ($subject['id'] ?? null) === $r['user']) {
                return self::GRANT;
            }
        }

        return self::ABSTAIN;
    }
}
