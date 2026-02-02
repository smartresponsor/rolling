<?php
declare(strict_types=1);

namespace App\Observability\Role\Tracing;

use OpenTelemetry\API\Trace\TracerProvider;
use Throwable;

/**
 *
 */

/**
 *
 */
final class OtelBridge
{
    private $tracer;

    /**
     * @param string $name
     */
    public function __construct(string $name = 'SmartResponsor/Role')
    {
        // Lazy detect OpenTelemetry\API
        if (class_exists(TracerProvider::class)) {
            $this->tracer = TracerProvider::getTracer($name);
        } else {
            $this->tracer = null;
        }
    }

    /**
     * @param string $name
     * @param array $attrs
     * @return object
     */
    public function startSpan(string $name, array $attrs = []): object
    {
        if ($this->tracer) {
            $span = $this->tracer->spanBuilder($name)->startSpan();
            foreach ($attrs as $k => $v) {
                try {
                    $span->setAttribute((string)$k, (string)$v);
                } catch (Throwable $e) {
                }
            }
            return (object)['span' => $span];
        }
        return (object)['span' => null];
    }

    /**
     * @param object $token
     * @param \Throwable|null $error
     * @return void
     */
    public function endSpan(object $token, ?Throwable $error = null): void
    {
        $span = $token->span ?? null;
        if ($span) {
            if ($error) {
                try {
                    $span->recordException($error);
                } catch (Throwable $e) {
                }
            }
            try {
                $span->end();
            } catch (Throwable $e) {
            }
        }
    }
}
