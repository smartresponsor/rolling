<?php

declare(strict_types=1);

namespace App\Rolling\Service\Http\Request;

use Symfony\Component\HttpFoundation\Request;

final class JsonPayloadReader
{
    /**
     * @return array<string,mixed>
     */
    public function readObject(Request $request): array
    {
        $content = trim((string) $request->getContent());
        if ('' === $content) {
            return [];
        }

        $decoded = json_decode($content, true);
        if (!is_array($decoded)) {
            return [];
        }

        /* @var array<string,mixed> $decoded */
        return $decoded;
    }

    /**
     * @return list<array<string,mixed>>
     */
    public function readList(Request $request): array
    {
        $content = trim((string) $request->getContent());
        if ('' === $content) {
            return [];
        }

        $decoded = json_decode($content, true);
        if (!is_array($decoded)) {
            return [];
        }

        $items = [];
        foreach ($decoded as $item) {
            if (is_array($item)) {
                /* @var array<string,mixed> $item */
                $items[] = $item;
            }
        }

        return $items;
    }
}
