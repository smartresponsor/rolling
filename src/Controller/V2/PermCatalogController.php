<?php
declare(strict_types=1);

namespace App\Controller\V2;

use App\Service\Permission\Catalog\CatalogService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 */

/**
 *
 */
final class PermCatalogController
{
    /**
     * @param \App\Legacy\Permission\Catalog\CatalogService $svc
     */
    public function __construct(private readonly CatalogService $svc)
    {
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $r
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function index(Request $r): JsonResponse
    {
        $component = $r->query->get('component');
        $snap = $this->svc->snapshot($component ? (string)$component : null);
        $etag = '"' . substr($snap['version'], 0, 16) . '"';
        $reqEtag = $r->headers->get('If-None-Match');
        if ($reqEtag && $reqEtag === $etag) {
            $resp = new JsonResponse(null, 304);
            $resp->headers->set('ETag', $etag);
            return $resp;
        }
        $resp = new JsonResponse($snap);
        $resp->headers->set('ETag', $etag);
        return $resp;
    }
}
