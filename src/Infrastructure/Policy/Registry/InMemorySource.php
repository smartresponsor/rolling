<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Policy\Registry;

final class InMemorySource implements SourceInterface
{
    /**
     * @param array<string,mixed> $config
     */
    public function __construct(private readonly array $config)
    {
    }

    public function get(): array
    {
        return $this->config;
    }
}
