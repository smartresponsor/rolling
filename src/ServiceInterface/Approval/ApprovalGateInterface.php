<?php
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\ServiceInterface\Approval;

/**
 *
 */

/**
 *
 */
interface ApprovalGateInterface
{
    /**
     * If decision demands four-eyes, create approval and return gated result.
     * @param array $decision
     * @param array $subject
     * @param string $action
     * @param array $resource
     * @return array<string,mixed>
     */
    public function gate(array $decision, array $subject, string $action, array $resource): array;

    /** @return array<string,mixed>|null final decision if approved */
    public function resolve(string $id): ?array;
}
