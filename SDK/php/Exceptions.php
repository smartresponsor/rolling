<?php
declare(strict_types=1);

namespace Role\SDK\V2;

use RuntimeException;

/**
 *
 */

/**
 *
 */
final class Exceptions extends RuntimeException
{
    /**
     * @param int $status
     * @param array $headers
     * @param string $body
     * @param string $message
     */
    public function __construct(public int $status, public array $headers, public string $body, string $message = 'Role API error')
    {
        parent::__construct($message, $status);
    }
}
