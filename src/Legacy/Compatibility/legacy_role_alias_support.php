<?php

declare(strict_types=1);

if (!function_exists('legacy_role_alias')) {
    /**
     * @param class-string $modern
     * @param class-string $legacy
     */
    function legacy_role_alias(string $modern, string $legacy): void
    {
        if ($modern === $legacy) {
            return;
        }

        if (
            !class_exists($legacy, false)
            && !interface_exists($legacy, false)
            && !trait_exists($legacy, false)
            && !enum_exists($legacy, false)
            && (
                class_exists($modern)
                || interface_exists($modern)
                || trait_exists($modern)
                || enum_exists($modern)
            )
        ) {
            class_alias($modern, $legacy);
        }
    }
}
