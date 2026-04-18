<?php

declare(strict_types=1);

<<<<<<< HEAD
namespace App\Entity\Role;
=======
namespace src\Entity\Role;

/**
 *
 */
>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa

final class Scope
{
<<<<<<< HEAD
    private function __construct(private readonly string $k)
    {
    }
=======
    /**
     * @param string $k
     */
    private function __construct(private readonly string $k) {}
>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa

    public static function global(): self
    {
        return new self('global');
    }

    public static function tenant(string $tenantId): self
    {
        return new self('tenant:' . $tenantId);
    }

    public static function resource(string $tenantId, string $resId): self
    {
        return new self('resource:' . $tenantId . ':' . $resId);
    }

    public function key(): string
    {
        return $this->k;
    }

    public function type(): string
    {
<<<<<<< HEAD
        return str_contains($this->k, ':') ? explode(':', $this->k, 2)[0] : $this->k;
=======
        return explode(':', $this->k, 2)[0];
>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa
    }

    public function tenantId(): ?string
    {
        $parts = explode(':', $this->k);

<<<<<<< HEAD
        return match ($parts[0] ?? $this->k) {
            'tenant' => $parts[1] ?? null,
            'resource' => $parts[1] ?? null,
            default => null,
        };
=======
        return $parts[0] === 'tenant' || $parts[0] === 'resource' ? ($parts[1] ?? null) : null;
>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa
    }

    public function resourceId(): ?string
    {
        $parts = explode(':', $this->k);

<<<<<<< HEAD
        return (($parts[0] ?? '') === 'resource') ? ($parts[2] ?? null) : null;
    }

    public function __toString(): string
    {
        return $this->k;
=======
        return $parts[0] === 'resource' ? ($parts[2] ?? null) : null;
>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa
    }
}
