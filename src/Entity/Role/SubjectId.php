<?php
declare(strict_types=1);

namespace App\Entity\Role;

final class SubjectId
{
    public function __construct(private readonly string $v)
    {
    }

    public function value(): string
    {
        return $this->v;
    }

    public function __toString(): string
    {
        return $this->v;
    }
}
