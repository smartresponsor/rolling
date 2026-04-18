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

final class SubjectId
{
<<<<<<< HEAD
    public function __construct(private readonly string $v)
    {
    }
=======
    /**
     * @param string $v
     */
    public function __construct(private readonly string $v) {}
>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa

    public function value(): string
    {
        return $this->v;
    }

    public function __toString(): string
    {
        return $this->v;
    }
}
