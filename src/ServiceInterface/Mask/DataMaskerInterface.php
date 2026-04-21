<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Mask;

interface DataMaskerInterface
{
    /**
     * @param array $data  @return array<string,mixed>
     * @param array $rules
     *
     * @return array
     */
    public function mask(array $data, array $rules): array;
}
