<?php

declare(strict_types=1);

<<<<<<< HEAD:src/Legacy/Shadow/Sampler/PercentageSampler.php
namespace App\Legacy\Shadow\Sampler;
=======
namespace App\Shadow\Role\Sampler;

>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa:src/Shadow/Role/Sampler/PercentageSampler.php
/**
 *
 */

/**
 *
 */
final class PercentageSampler
{
    /**
     * @param float $ratio
     * @param int|null $seed
     */
    public function __construct(private readonly float $ratio = 0.05, private readonly ?int $seed = null) {}

    /**
     * @param string|null $key
     * @return bool
     */
    public function hit(?string $key = null): bool
    {
        if ($this->ratio <= 0) {
            return false;
        }
        if ($this->ratio >= 1) {
            return true;
        }
        if ($this->seed !== null) {
            mt_srand($this->seed ^ crc32((string) $key));
        }
        $r = mt_rand() / mt_getrandmax();
        return $r < $this->ratio;
    }
}
