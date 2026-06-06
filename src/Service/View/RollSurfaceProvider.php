<?php

declare(strict_types=1);

namespace App\Rolling\Service\View;

use App\Rolling\ServiceInterface\Administration\RollingAclHealthReportProviderInterface;
use App\Rolling\ServiceInterface\View\RollSurfaceProviderInterface;
use App\Rolling\Value\View\RollSurfaceOutput;

/**
 * Produces the canonical Rolling surface data for the roll template folder.
 *
 * This class intentionally does not depend on Interfacing classes, template
 * namespaces, Twig loaders, or bridge contracts. It exports named slots only.
 */
final readonly class RollSurfaceProvider implements RollSurfaceProviderInterface
{
    public function __construct(
        private RollingAclHealthReportProviderInterface $healthReportProvider,
    ) {
    }

    public function summary(): RollSurfaceOutput
    {
        $health = $this->healthReportProvider->report()->toSafeArray();
        $summary = $this->arrayValue($health, 'summary');
        $checks = $this->listValue($health, 'checks');
        $guards = $this->listValue($health, 'guards');

        return new RollSurfaceOutput(
            'summary',
            [
                'title' => 'Roll',
                'subtitle' => 'Rolling authorization and role administration surface',
                'summary' => $summary,
                'checks' => $checks,
                'guards' => $guards,
                'actions' => [
                    [
                        'label' => 'Review role hierarchy',
                        'route' => 'administering_rolling_role_hierarchy',
                    ],
                    [
                        'label' => 'Open configuration center',
                        'route' => 'administration_config_center_index',
                    ],
                ],
                'empty' => [
                    'message' => 'Rolling did not return any health checks yet.',
                ],
                'meta' => [
                    'component' => 'Rolling',
                    'businessWord' => 'roll',
                    'templateFolder' => 'templates/roll',
                    'fallback' => 'Render these slots as safe data when templates/roll/summary.html.twig is absent.',
                ],
                'data' => $health,
            ],
        );
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return array<string, mixed>
     */
    private function arrayValue(array $payload, string $key): array
    {
        $value = $payload[$key] ?? [];

        return is_array($value) ? $value : [];
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return list<mixed>
     */
    private function listValue(array $payload, string $key): array
    {
        $value = $payload[$key] ?? [];

        if (!is_array($value)) {
            return [];
        }

        return array_values($value);
    }
}
