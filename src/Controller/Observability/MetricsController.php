<?php

declare(strict_types=1);

namespace App\Rolling\Controller\Observability;

use App\Rolling\Infrastructure\Observability\Metrics\PrometheusExporter;
use Symfony\Component\HttpFoundation\Response;

final class MetricsController
{
    /**
     * @param PrometheusExporter $exporter
     */
    public function __construct(private readonly PrometheusExporter $exporter)
    {
    }

    /**
     * @return Response
     */
    public function metrics(): Response
    {
        $text = $this->exporter->render(); // формат Prometheus text 0.0.4

        return new Response($text, 200, ['Content-Type' => 'text/plain; version=0.0.4']);
    }
}
