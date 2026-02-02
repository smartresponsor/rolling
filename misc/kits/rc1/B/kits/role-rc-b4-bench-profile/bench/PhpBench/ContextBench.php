<?php
declare(strict_types=1);

/**
 *
 */

/**
 *
 */
class ContextBench
{
    private array $ctx;

    public function __construct()
    {
        $this->ctx = ['z' => 3, 'a' => ['k' => 2, 'b' => 1], 'm' => 'str', 'num' => 42];
    }

    /**
     * @param array $a
     * @return array
     */
    private function norm(array $a): array
    {
        ksort($a);
        foreach ($a as $k => $v) if (is_array($v)) $a[$k] = $this->norm($v);
        return $a;
    }

    /**
     * @Revs(1000)
     * @Iterations(10)
     */
    public function benchNormalizeAndEncode(): void
    {
        $x = hash('sha256', json_encode($this->norm($this->ctx), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        if (empty($x)) throw new RuntimeException();
    }
}
