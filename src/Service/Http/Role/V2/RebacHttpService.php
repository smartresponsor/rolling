<?php

declare(strict_types=1);

namespace App\Rolling\Service\Http\Role\V2;

use App\Rolling\DTO\Http\Role\RebacCheckPayload;
use App\Rolling\DTO\Http\Role\RebacWritePayload;
use App\Rolling\Infrastructure\Rebac\Tuple;
use App\Rolling\Service\Http\Request\JsonPayloadReader;
use App\Rolling\Service\Rebac\RebacRelationshipChecker;
use App\Rolling\Service\Rebac\RebacRelationshipWriter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class RebacHttpService
{
    public function __construct(private readonly RebacRelationshipWriter $writer, private readonly RebacRelationshipChecker $checker, private readonly JsonPayloadReader $payloadReader)
    {
    }

    public function write(Request $req): JsonResponse
    {
        $payload = RebacWritePayload::fromArray($this->payloadReader->readObject($req));
        $ts = [];
        foreach ($payload->tuples as $row) {
            $ts[] = Tuple::fromArray([
                'ns' => $payload->namespace,
                'obj_type' => (string) $row['obj_type'],
                'obj_id' => (string) $row['obj_id'],
                'relation' => (string) $row['relation'],
                'subj_type' => (string) $row['subj_type'],
                'subj_id' => (string) $row['subj_id'],
                'subj_rel' => isset($row['subj_rel']) ? (string) $row['subj_rel'] : null,
            ]);
        }
        $rev = $this->writer->write($payload->namespace, $ts);

        return new JsonResponse(['ok' => true, 'rev' => (string) $rev]);
    }

    public function check(Request $req): JsonResponse
    {
        $payload = RebacCheckPayload::fromArray($this->payloadReader->readObject($req));
        $res = $this->checker->check($payload->namespace, $payload->subject, $payload->object, $payload->relation);

        return new JsonResponse($res);
    }
}
