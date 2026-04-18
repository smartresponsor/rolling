<?php

declare(strict_types=1);

namespace App\Legacy\Model\Rebac;

/**
 *
 */

/**
 *
 */
final class Tuple
{
    /**
     * @param string $ns
     * @param string $objType
     * @param string $objId
     * @param string $relation
     * @param string $subjType
     * @param string $subjId
     * @param string|null $subjRel
     */
    public function __construct(
        public string  $ns,
        public string  $objType,
        public string  $objId,
        public string  $relation,
        public string  $subjType,
        public string  $subjId,
        public ?string $subjRel = null,
    ) {}

    /**
     * @param array $a
     * @return \App\Legacy\Model\Rebac\Tuple
     */
    public static function fromArray(array $a): self
    {
        return new self(
            (string) $a['ns'],
            (string) $a['obj_type'],
            (string) $a['obj_id'],
            (string) $a['relation'],
            (string) $a['subj_type'],
            (string) $a['subj_id'],
            isset($a['subj_rel']) ? (string) $a['subj_rel'] : null,
        );
    }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return [
            'ns' => $this->ns,
            'obj_type' => $this->objType,
            'obj_id' => $this->objId,
            'relation' => $this->relation,
            'subj_type' => $this->subjType,
            'subj_id' => $this->subjId,
            'subj_rel' => $this->subjRel,
        ];
    }

    /**
     * @return string
     */
    public function objectKey(): string
    {
        return $this->objType . ':' . $this->objId;
    }

    /**
     * @return string
     */
    public function subjectKey(): string
    {
        return $this->subjType . ':' . $this->subjId . ($this->subjRel ? '#' . $this->subjRel : '');
    }
}
