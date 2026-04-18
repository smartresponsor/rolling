<?php

declare(strict_types=1);

namespace App\Service\Consistency;

final class TokenSet
{
    public function __construct(
        public int $policyRev,
        public int $rebacRev,
        public ?int $subjectEpoch = null,
    ) {
    }

    public static function fromString(string $s): self
    {
        $parts = [];
        foreach (explode(';', $s) as $kv) {
            if ($kv === '') {
                continue;
            }
            [$k, $v] = array_pad(explode(':', $kv, 2), 2, null);
            $parts[(string) $k] = $v !== null ? (int) $v : null;
        }

        return new self((int) ($parts['p'] ?? 0), (int) ($parts['r'] ?? 0), isset($parts['s']) ? (int) $parts['s'] : null);
    }

    public function __toString(): string
    {
        $serialized = "p:{$this->policyRev};r:{$this->rebacRev};";
        if ($this->subjectEpoch !== null) {
            $serialized .= "s:{$this->subjectEpoch};";
        }

        return $serialized;
    }

    public function hash(): string
    {
        return sha1((string) $this);
    }
}
