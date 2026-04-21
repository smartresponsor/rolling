<?php

declare(strict_types=1);

namespace App\Rolling\Service\Audit;

final class Redactor
{
    /** @var array */
    private array $maskFields;
    /** @var array */
    private array $redactRules;

    /**
     * @param array $maskFields  dot-path fields to fully mask (e.g. "context.ssn", "resource")
     * @param array $redactRules regex-based replacements inside string fields
     */
    public function __construct(array $maskFields = [], array $redactRules = [])
    {
        $this->maskFields = $maskFields;
        $this->redactRules = $redactRules;
    }

    /**
     * @param array $event
     *
     * @return array
     */
    public function apply(array $event): array
    {
        foreach ($this->maskFields as $path) {
            $event = $this->maskPath($event, $path);
        }
        foreach ($this->redactRules as $rule) {
            $event = $this->redactPath($event, $rule['path'] ?? '', $rule['pattern'] ?? '');
        }

        return $event;
    }

    /**
     * @param array  $event
     * @param string $path
     *
     * @return array
     */
    private function maskPath(array $event, string $path): array
    {
        $parts = '' === $path ? [] : explode('.', $path);
        if (!$parts) {
            return $event;
        }
        $ref = &$event;
        for ($i = 0; $i < count($parts) - 1; ++$i) {
            $k = $parts[$i];
            if (!is_array($ref) || !array_key_exists($k, $ref)) {
                return $event;
            }
            $ref = &$ref[$k];
        }
        $last = $parts[count($parts) - 1];
        if (is_array($ref) && array_key_exists($last, $ref)) {
            $ref[$last] = $this->maskValue($ref[$last]);
        }

        return $event;
    }

    /**
     * @param array  $event
     * @param string $path
     * @param string $pattern
     *
     * @return array
     */
    private function redactPath(array $event, string $path, string $pattern): array
    {
        if ('' === $path || '' === $pattern) {
            return $event;
        }
        $parts = explode('.', $path);
        $ref = &$event;
        for ($i = 0; $i < count($parts) - 1; ++$i) {
            $k = $parts[$i];
            if (!is_array($ref) || !array_key_exists($k, $ref)) {
                return $event;
            }
            $ref = &$ref[$k];
        }
        $last = $parts[count($parts) - 1];
        if (is_array($ref) && array_key_exists($last, $ref) && is_string($ref[$last])) {
            $ref[$last] = (string) preg_replace('~'.$pattern.'~', '[REDACTED]', $ref[$last]);
        }

        return $event;
    }

    /**
     * @param mixed $v
     *
     * @return string
     */
    private function maskValue(mixed $v): string
    {
        if (is_string($v)) {
            return str_repeat('*', min(8, max(3, (int) (strlen($v) / 2))));
        }
        if (is_array($v)) {
            return '[MASKED_ARRAY]';
        }
        if (is_object($v)) {
            return '[MASKED_OBJECT]';
        }

        return '[MASKED]';
    }
}
