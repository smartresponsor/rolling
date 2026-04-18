<?php

declare(strict_types=1);

namespace App\Infrastructure\Rebac;

final class Tuple
{
    public function __construct(
        public readonly string $ns,
        public readonly string $objType,
        public readonly string $objId,
        public readonly string $relation,
        public readonly string $subjType,
        public readonly string $subjId,
        public readonly ?string $subjRel = null,
    ) {
    }

    /**
     * @param array<string,mixed> $a
     */
    public static function fromArray(array $a): self
    {
        return new self(
            (string) ($a['ns'] ?? 'default'),
            (string) $a['objType'],
            (string) $a['objId'],
            (string) $a['relation'],
            (string) $a['subjType'],
            (string) $a['subjId'],
            isset($a['subjRel']) ? (string) $a['subjRel'] : null,
        );
    }

    /** @return array{ns:string,objType:string,objId:string,relation:string,subjType:string,subjId:string,subjRel:?string} */
    public function toArray(): array
    {
        return [
            'ns' => $this->ns,
            'objType' => $this->objType,
            'objId' => $this->objId,
            'relation' => $this->relation,
            'subjType' => $this->subjType,
            'subjId' => $this->subjId,
            'subjRel' => $this->subjRel,
        ];
    }
}
