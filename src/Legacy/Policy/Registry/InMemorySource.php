<?php
declare(strict_types=1);

namespace App\Legacy\Policy\Registry;

/**
 *
 */

/**
 *
 */
final class InMemorySource implements SourceInterface
{
    /**
     * @param array $config
     */
    public function __construct(private readonly array $config)
    {
    }

    /**
     * @return array
     */
    public function get(): array
    {
        return $this->config;
    }
}
