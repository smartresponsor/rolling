<?php

declare(strict_types=1);

namespace App\Rolling\Value\Administration;

/**
 * Canonical Rolling scope chain for Managing field-level decisions.
 *
 * The full PHP resource FQCN stays in request attributes. Scopes use a bounded resource key so existing Rolling
 * scope columns can safely store field, page, resource, component, and global grants without EasyAdmin coupling.
 */
final readonly class RollingFieldAccessScopeSet
{
    /** @param non-empty-list<string> $scopes */
    private function __construct(private array $scopes)
    {
    }

    public static function fromRequest(RollingFieldAccessDecisionRequest $request): self
    {
        $component = self::scopeToken($request->componentKey, 'component');
        $resource = self::resourceToken($request->resourceClass);
        $page = self::scopeToken($request->pageName, 'page');
        $field = self::scopeToken($request->fieldName, 'field');

        return new self([
            sprintf('component:%s:resource:%s:page:%s:field:%s', $component, $resource, $page, $field),
            sprintf('component:%s:resource:%s:page:%s', $component, $resource, $page),
            sprintf('component:%s:resource:%s', $component, $resource),
            sprintf('component:%s', $component),
            'global',
        ]);
    }

    /** @return non-empty-list<string> */
    public function scopes(): array
    {
        return $this->scopes;
    }

    public function mostSpecificScope(): string
    {
        return $this->scopes[0];
    }

    private static function resourceToken(string $resourceClass): string
    {
        $resourceClass = trim($resourceClass, '\\');
        $normalized = self::scopeToken(str_replace('\\', '.', $resourceClass), 'resource');

        if (strlen($normalized) <= 96) {
            return $normalized;
        }

        $parts = explode('.', $normalized);
        $basename = end($parts) ?: 'resource';

        return sprintf('%s.%s', substr($basename, 0, 48), substr(sha1($normalized), 0, 16));
    }

    private static function scopeToken(string $value, string $fallback): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9_.-]+/', '-', $value) ?? '';
        $value = trim($value, '-_.');

        return '' !== $value ? $value : $fallback;
    }
}
