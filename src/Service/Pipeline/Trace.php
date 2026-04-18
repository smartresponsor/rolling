<?php

declare(strict_types=1);
/* Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp */

<<<<<<< HEAD:src/Service/Pipeline/Trace.php
namespace App\Service\Pipeline;
=======
namespace Pipeline;

>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa:src/Service/Role/Pipeline/Trace.php
/**
 *
 */

/**
 *
 */
final class Trace
{
    /** @var array */
    private array $steps = [];

    /**
     * @param string $stage
     * @param string $msg
     * @param array $data
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
