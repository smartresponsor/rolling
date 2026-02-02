<?php
declare(strict_types=1);

namespace Policy\Role\V2;

use Policy\Role\Obligation\Obligations;

/**
 *
 */

/**
 *
 */
final class DecisionWithObligations
{
    /**
     * @param bool $allow
     * @param string $reason
     * @param \Policy\Role\Obligation\Obligations $obligations
     */
    public function __construct(private readonly bool $allow, public string $reason, public Obligations $obligations)
    {
    }

    /**
     * @param string $reason
     * @param \Policy\Role\Obligation\Obligations $o
     * @return self
     */
    public static function allow(string $reason, Obligations $o): self
    {
        return new self(true, $reason, $o);
    }

    /**
     * @param string $reason
     * @param \Policy\Role\Obligation\Obligations $o
     * @return self
     */
    public static function deny(string $reason, Obligations $o): self
    {
        return new self(false, $reason, $o);
    }

    /**
     * @return bool
     */
    public function isAllow(): bool
    {
        return $this->allow;
    }
}
