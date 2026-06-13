<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Observability\Tracing;

final class OtelBridge
{
    private ?object $tracer;

    public function __construct(string $nameEntity = 'SmartResponsor/Role')
    {
        $providerClass = 'OpenTelemetry\\API\\Trace\\TracerProvider';
        if (class_exists($providerClass) && method_exists($providerClass, 'getTracer')) {
            /* @var object $providerClass */
            $this->tracer = $providerClass::getTracer($nameEntity);
        } else {
            $this->tracer = null;
        }
    }

    /**
     * @param array<string, scalar|null> $attrs
     */
    public function startSpan(string $nameEntity, array $attrs = []): object
    {
        if (null !== $this->tracer && method_exists($this->tracer, 'spanBuilder')) {
            $span = $this->tracer->spanBuilder($nameEntity)->startSpan();
            foreach ($attrs as $key => $value) {
                try {
                    $span->setAttribute((string) $key, $value);
                } catch (\Throwable $e) {
                    error_log('OtelBridge::startSpan attribute failure: '.$e->getMessage());
                }
            }

            return (object) ['span' => $span];
        }

        return (object) ['span' => null];
    }

    public function endSpan(object $token, ?\Throwable $error = null): void
    {
        $span = $token->span ?? null;
        if (!is_object($span)) {
            return;
        }

        if (null !== $error) {
            try {
                $span->recordException($error);
            } catch (\Throwable $e) {
                error_log('OtelBridge::recordException failure: '.$e->getMessage());
            }
        }

        try {
            $span->end();
        } catch (\Throwable $e) {
            error_log('OtelBridge::endSpan end failure: '.$e->getMessage());
        }
    }
}
