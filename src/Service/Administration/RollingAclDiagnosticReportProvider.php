<?php

declare(strict_types=1);

namespace App\Rolling\Service\Administration;

use App\Rolling\ServiceInterface\Administration\RollingAclDiagnosticReportProviderInterface;
use App\Rolling\ServiceInterface\Administration\RollingAclHealthReportProviderInterface;
use App\Rolling\Value\Administration\RollingAclDiagnosticIssue;
use App\Rolling\Value\Administration\RollingAclDiagnosticReport;

/**
 * Builds a safe issue register from Rolling-owned ACL health checks.
 */
final readonly class RollingAclDiagnosticReportProvider implements RollingAclDiagnosticReportProviderInterface
{
    public function __construct(
        private RollingAclHealthReportProviderInterface $healthReportProvider,
    ) {
    }

    public function report(): RollingAclDiagnosticReport
    {
        $issues = [];
        foreach ($this->healthReportProvider->report()->checks() as $check) {
            $data = $check->toSafeArray();
            $status = (string) $data['status'];
            $severity = (string) $data['severity'];
            $blocking = (bool) ($data['blocking'] ?? false);

            if (!$blocking && 'healthy' === $status && 'info' === $severity) {
                continue;
            }

            $issues[] = new RollingAclDiagnosticIssue(
                (string) $data['key'],
                (string) $data['label'],
                (string) $data['category'],
                $severity,
                $status,
                $blocking,
                is_array($data['context'] ?? null) ? $data['context'] : [],
            );
        }

        if ([] === $issues) {
            $issues[] = new RollingAclDiagnosticIssue(
                'rolling.diagnostics.clear',
                'Rolling ACL diagnostics have no active blocking issue',
                'diagnostics',
                'info',
                'clear',
                false,
                [
                    'owner' => 'Rolling',
                    'administeringRole' => 'safe_visualizer',
                ],
            );
        }

        return new RollingAclDiagnosticReport(
            new \DateTimeImmutable(),
            $issues,
            [
                'Diagnostics do not expose raw subject grants, raw policy internals, sessions, passwords, or secrets.',
                'Rolling remains the owner of ACL authorization and mutation execution.',
            ],
        );
    }
}
