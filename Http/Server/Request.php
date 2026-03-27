<?php

declare(strict_types=1);

namespace Http\Server;

/**
 *
 */

/**
 *
 */
interface Request
{
    /**
     * @return string
     */
    public function method(): string;

    /**
     * @return string
     */
    public function path(): string;

    /**
     * @param string $name
     * @return string|null
     */
    public function header(string $name): ?string;

    /**
     * @return string
     */
    public function body(): string;
}
