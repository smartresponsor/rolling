<?php
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace src\ServiceInterface\Role\Obligation;

/**
 *
 */

/**
 *
 */
interface ObligationRunnerInterface
{
    /**
     * Apply obligations over decision/subject/resource and return post-processed tuple.
     * @param array $decision # may include ['obligations'=>string[]]
     * @param array $subject
     * @param array $resource
     * @return array{decision: array<string,mixed>, subject: array<string,mixed>, resource: array<string,mixed>, effects: array<int,string>}
     */
    public function apply(array $decision, array $subject, array $resource): array;
}
