<?php

declare(strict_types=1);

namespace App\Policy\V2;

use App\Policy\Obligation\Obligations;

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
    public function __construct(private readonly bool $allow, public string $reason, public Obligations $obligations) {}

    /**
     * @param string $reason
     * @param \Policy\Role\Obligation\Obligations $o
     * @return self
     */
    public static function allow(string $reason, ?Obligations $o = null): self
    {
        return new self(true, $reason, $o ?? Obligations::empty());
    }

    /**
     * @param string $reason
     * @param \Policy\Role\Obligation\Obligations $o
     * @return self
     */
<<<<<<< HEAD:src/Policy/V2/DecisionWithObligations.php
    public static function allow(string $reason, ?Obligations $o = null): self
    {
        return new self(true, $reason, $o ?? Obligations::empty());
    }

    /**
     * @param string $reason
     * @param \Policy\Role\Obligation\Obligations $o
     * @return self
     */
    public static function deny(string $reason, ?Obligations $o = null): self
    {
=======
    public static function deny(string $reason, ?Obligations $o = null): self
    {
>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa:Policy/Role/V2/DecisionWithObligations.php
        return new self(false, $reason, $o ?? Obligations::empty());
    }

    /**
     * @return bool
     */
    public function isAllow(): bool
    {
        return $this->allow;
    }

    public function isDeny(): bool
    {
        return !$this->allow;
    }
}
