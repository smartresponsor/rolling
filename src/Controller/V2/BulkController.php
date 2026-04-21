<?php

declare(strict_types=1);

namespace App\Rolling\Controller\V2;

use App\Rolling\Entity\Role\PermissionKey;
use App\Rolling\Entity\Role\Scope;
use App\Rolling\Entity\Role\SubjectId;
use App\Rolling\Integration\Http\V2\Bulk\CsvReader;
use App\Rolling\Integration\Http\V2\Bulk\NdjsonReader;
use App\Rolling\Integration\Http\V2\Bulk\NdjsonWriter;
use App\Rolling\Service\Consistency\Composer;
use App\Rolling\ServiceInterface\Policy\PdpV2Interface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class BulkController
{
    /**
     * @param Composer $composer
     */
    public function __construct(private readonly PdpV2Interface $pdp, private readonly Composer $composer)
    {
    }

    public function stream(Request $req): StreamedResponse
    {
        $ctype = strtolower((string) $req->headers->get('content-type', 'application/x-ndjson'));
        $reader = str_contains($ctype, 'csv') ? new CsvReader() : new NdjsonReader();
        $resp = new StreamedResponse(function () use ($reader) {
            $writer = new NdjsonWriter();
            $in = fopen('php://input', 'r');
            if (false === $in) {
                return;
            }
            while (true) {
                $pos = ftell($in);
                $line = fgets($in);
                if (false === $line) {
                    break;
                }
                fseek($in, $pos);
                foreach ($reader->items($in) as $it) {
                    $sc = (array) $it['scope'];
                    $type = (string) ($sc['type'] ?? 'global');
                    $scope = match ($type) {
                        'tenant' => Scope::tenant((string) ($sc['tenantId'] ?? '')),
                        'resource' => Scope::resource((string) ($sc['resourceId'] ?? ''), (string) ($sc['key'] ?? 'resource'), isset($sc['tenantId']) ? (string) $sc['tenantId'] : null),
                        default => Scope::global(),
                    };
                    $s = new SubjectId((string) $it['subject']);
                    $a = new PermissionKey((string) $it['action']);
                    $ctx = (array) $it['context'];
                    $dec = $this->pdp->check($s, $a, $scope, $ctx);
                    $rev = (string) $this->composer->token((string) $s);
                    $writer->write(fopen('php://output', 'w'), ['subject' => (string) $s, 'action' => (string) $a, 'allow' => $dec->isAllow(), 'reason' => $dec->reason(), 'obligations' => $dec->obligations()->toArray(), 'rev' => $rev]);
                    @ob_flush();
                    @flush();
                }
            }
        });
        $resp->headers->set('Content-Type', 'application/x-ndjson');

        return $resp;
    }
}
