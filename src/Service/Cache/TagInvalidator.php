<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\Service\Cache;

use App\Rolling\ServiceInterface\Cache\TagInvalidatorInterface;

/**
 * File-based tag version store under /tmp (portable).
 */
final class TagInvalidator implements TagInvalidatorInterface
{
    private string $path;
    /** @var array */
    private array $versions = [];

    /**
     * @param string $path
     */
    public function __construct(string $path = '/tmp/role_tag_versions.json')
    {
        $this->path = $path;
        $this->load();
    }

    /**
     * @param string $tag
     *
     * @return void
     */
    public function bumpTag(string $tag): void
    {
        $this->versions[$tag] = ($this->versions[$tag] ?? 0) + 1;
        $this->persist();
    }

    /** @param string[] $tags */
    public function invalidateTags(array $tags): void
    {
        foreach ($tags as $t) {
            $this->bumpTag($t);
        }
    }

    /**
     * @param string $tag
     *
     * @return int
     */
    public function getTagVersion(string $tag): int
    {
        return (int) ($this->versions[$tag] ?? 0);
    }

    /**
     * @return void
     */
    private function load(): void
    {
        if (is_file($this->path)) {
            $d = json_decode((string) file_get_contents($this->path), true);
            if (is_array($d)) {
                $this->versions = array_map('intval', $d);
            }
        }
    }

    /**
     * @return void
     */
    private function persist(): void
    {
        @file_put_contents($this->path, json_encode($this->versions));
    }
}
