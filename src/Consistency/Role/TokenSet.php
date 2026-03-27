<?php

declare(strict_types=1);

namespace App\Consistency\Role;

/**
 *
 */
final class TokenSet
{
    /**
     * @param int $policyRev
     * @param int $rebacRev
     * @param int|null $subjectEpoch
     */
    public function __construct(
        public int  $policyRev,
        public int  $rebacRev,
        public ?int $subjectEpoch = null,
    ) {}

    /**
     * @return self
     */
    public static function fromString(string $s): self
    {
        $parts = [];
        foreach (explode(';', $s) as $kv) {
            if ($kv === '') {
                continue;
            }
            [$k, $v] = array_pad(explode(':', $kv, 2), 2, null);
            $parts[$k] = $v !== null ? (int) $v : null;
        }
        return new self((int) ($parts['p'] ?? 0), (int) ($parts['r'] ?? 0), isset($parts['s']) ? (int) $parts['s'] : null);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $s = "p:{$this->policyRev};r:{$this->rebacRev};";
        if ($this->subjectEpoch !== null) {
            $s .= "s:{$this->subjectEpoch};";
        }
        return $s;
    }

    /**
     * @return string
     */
    public function hash(): string
    {
        return sha1((string) $this);
    }
}
