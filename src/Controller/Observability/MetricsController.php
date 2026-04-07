<?php
declare(strict_types=1);

namespace App\Controller\Observability;

use Symfony\Component\HttpFoundation\Response;
use App\Infrastructure\Observability\Metrics\PrometheusExporter;

/**
 *
 */

/**
 *
 */
final class MetricsController
{
    /**
     * @param \App\Infrastructure\Observability\Metrics\PrometheusExporter $exporter
     */
    public function __construct(private readonly PrometheusExporter $exporter)
    {
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function metrics(): Response
    {
        $text = $this->exporter->render(); // формат Prometheus text 0.0.4
        return new Response($text, 200, ['Content-Type' => 'text/plain; version=0.0.4']);
    }
}
