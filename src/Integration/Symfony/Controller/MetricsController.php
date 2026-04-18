<?php

declare(strict_types=1);

namespace App\Integration\Symfony\Controller;

use App\Controller\Observability\MetricsController as CanonicalMetricsController;

final class MetricsController extends CanonicalMetricsController
{
<<<<<<< HEAD
=======
    /**
     * @param \App\Observability\Role\Metrics\PrometheusExporter $exporter
     */
    public function __construct(private readonly PrometheusExporter $exporter) {}

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function metrics(): Response
    {
        $text = $this->exporter->render(); // формат Prometheus text 0.0.4
        return new Response($text, 200, ['Content-Type' => 'text/plain; version=0.0.4']);
    }
>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa
}
