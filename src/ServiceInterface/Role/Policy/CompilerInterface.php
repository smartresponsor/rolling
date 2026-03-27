<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace src\ServiceInterface\Role\Policy;

/**
 * Compiles a PEL (Policy Expression Language) file into a PHP evaluator.
 * Returns path to generated PHP file which returns a callable with signature:
 * function(array $subject, string $action, array $resource, array $context): array{allowed:bool, ruleId:string, reason:string}
 */
interface CompilerInterface
{
    /**
     * @param string $name
     * @param string $inputPath
     * @param string|null $outDir
     * @return string
     */
    public function compile(string $name, string $inputPath, ?string $outDir = null): string;
}
