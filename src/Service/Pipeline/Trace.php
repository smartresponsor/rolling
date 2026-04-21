<?php

declare(strict_types=1);
/* Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp */

namespace App\Rolling\Service\Pipeline;

final class Trace
{
    /** @var array */
    private array $steps = [];

    /**
     * @param string $stage
     * @param string $msg
     * @param array  $data
     *
     * @return void
     */
    public function add(string $stage, string $msg, array $data = []): void
    {
        $this->steps[] = ['stage' => $stage, 'msg' => $msg, 'data' => $data, 'ts' => gmdate('c')];
    }

    /** @return array<int,array<string,mixed>> */
    public function all(): array
    {
        return $this->steps;
    }
}
