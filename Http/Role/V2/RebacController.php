<?php
declare(strict_types=1);

namespace Http\Role\V2;

use App\Model\Role\Rebac\Tuple;
use App\Service\Role\Rebac\{Checker, Writer};
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 */

/**
 *
 */
final class RebacController
{
    /**
     * @param \App\Service\Role\Rebac\Writer $writer
     * @param \App\Service\Role\Rebac\Checker $checker
     */
    public function __construct(private readonly Writer $writer, private readonly Checker $checker)
    {
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $req
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function write(Request $req): JsonResponse
    {
        /** @var array{ns:string,tuples:list<array<string,mixed>>} $in */
        $in = json_decode((string)$req->getContent(), true) ?? [];
        $ns = (string)($in['ns'] ?? 'default');
        $ts = [];
        foreach ((array)($in['tuples'] ?? []) as $row) {
            $ts[] = Tuple::fromArray([
                'ns' => $ns,
                'obj_type' => (string)$row['obj_type'],
                'obj_id' => (string)$row['obj_id'],
                'relation' => (string)$row['relation'],
                'subj_type' => (string)$row['subj_type'],
                'subj_id' => (string)$row['subj_id'],
                'subj_rel' => isset($row['subj_rel']) ? (string)$row['subj_rel'] : null,
            ]);
        }
        $rev = $this->writer->write($ns, $ts);
        return new JsonResponse(['ok' => true, 'rev' => (string)$rev]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $req
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function check(Request $req): JsonResponse
    {
        /** @var array{ns:string,subject:string,object:string,relation:string} $in */
        $in = json_decode((string)$req->getContent(), true) ?? [];
        $ns = (string)($in['ns'] ?? 'default');
        $subject = (string)$in['subject'];
        $object = (string)$in['object'];
        $relation = (string)$in['relation'];
        $res = $this->checker->check($ns, $subject, $object, $relation);
        return new JsonResponse($res);
    }
}
