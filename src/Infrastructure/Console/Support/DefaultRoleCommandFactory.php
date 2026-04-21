<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Console\Support;

use App\Rolling\Infrastructure\Console\Command\AdminPolicyActivateCommand;
use App\Rolling\Infrastructure\Console\Command\AdminPolicyExportCommand;
use App\Rolling\Infrastructure\Console\Command\AdminPolicyImportCommand;
use App\Rolling\Infrastructure\Console\Command\AdminRebacStatsCommand;
use App\Rolling\Infrastructure\Console\Command\AuditCommand;
use App\Rolling\Infrastructure\Console\Command\BatchPerfCommand;
use App\Rolling\Infrastructure\Console\Command\BenchBaselinePromoteCommand;
use App\Rolling\Infrastructure\Console\Command\BenchCommand;
use App\Rolling\Infrastructure\Console\Command\BenchProfileCheckCommand;
use App\Rolling\Infrastructure\Console\Command\BenchRegressionCheckCommand;
use App\Rolling\Infrastructure\Console\Command\BenchReportCommand;
use App\Rolling\Infrastructure\Console\Command\BenchStatsCommand;
use App\Rolling\Infrastructure\Console\Command\ExplainCommand;
use App\Rolling\Infrastructure\Console\Command\FixtureListCommand;
use App\Rolling\Infrastructure\Console\Command\FixtureShowCommand;
use App\Rolling\Infrastructure\Console\Command\FixtureSmokeCommand;
use App\Rolling\Infrastructure\Console\Command\JanitorArchiveAuditCommand;
use App\Rolling\Infrastructure\Console\Command\JanitorAuditGcCommand;
use App\Rolling\Infrastructure\Console\Command\JanitorGcCommand;
use App\Rolling\Infrastructure\Console\Command\JanitorReplayGcCommand;
use App\Rolling\Infrastructure\Console\Command\PerfBaselinePromoteCommand;
use App\Rolling\Infrastructure\Console\Command\PerfProfileCheckCommand;
use App\Rolling\Infrastructure\Console\Command\PerfRegressionCheckCommand;
use App\Rolling\Infrastructure\Console\Command\PerfReportCommand;
use App\Rolling\Infrastructure\Console\Command\PerfStatsCommand;
use App\Rolling\Infrastructure\Console\Command\PolicyActivateCommand;
use App\Rolling\Infrastructure\Console\Command\PolicyExportCommand;
use App\Rolling\Infrastructure\Console\Command\PolicyImportCommand;
use App\Rolling\Infrastructure\Console\Command\PolicyListCommand;
use App\Rolling\Infrastructure\Console\Command\PolicyMigrateCommand;
use App\Rolling\Infrastructure\Console\Command\ProfileAutoPromoteCommand;
use App\Rolling\Infrastructure\Console\Command\ProfileCatalogListCommand;
use App\Rolling\Infrastructure\Console\Command\ProfileReportCommand;
use App\Rolling\Infrastructure\Console\Command\RebacCheckCommand;
use App\Rolling\Infrastructure\Console\Command\RebacWriteCommand;
use App\Rolling\Infrastructure\Console\Command\ScenarioListCommand;
use App\Rolling\Infrastructure\Console\Command\ScenarioOperationCommand;
use App\Rolling\Infrastructure\Console\Command\ScenarioRunCommand;
use App\Rolling\Infrastructure\Console\Contract\RoleCommandFactoryInterface;

final class DefaultRoleCommandFactory implements RoleCommandFactoryInterface
{
    public function __construct(private readonly RoleConsoleRuntime $runtime)
    {
    }

    public function createCommands(): array
    {
        return [
            new FixtureListCommand(),
            new FixtureShowCommand(),
            new FixtureSmokeCommand(),
            new ScenarioListCommand(),
            new ScenarioRunCommand(),
            new ScenarioOperationCommand(
                'app:role:propagation:preview',
                'propagation',
                'preview',
                'Preview propagation scenario for a fixture.',
            ),
            new ScenarioOperationCommand(
                'app:role:propagation:run',
                'propagation',
                'run',
                'Run propagation scenario for a fixture.',
            ),
            new ScenarioOperationCommand(
                'app:role:elimination:preview',
                'elimination',
                'preview',
                'Preview elimination scenario for a fixture.',
            ),
            new ScenarioOperationCommand(
                'app:role:elimination:run',
                'elimination',
                'run',
                'Run elimination scenario for a fixture.',
            ),
            new ExplainCommand(),
            new AuditCommand(),
            new RebacWriteCommand($this->runtime),
            new RebacCheckCommand($this->runtime),
            new PolicyImportCommand($this->runtime),
            new PolicyActivateCommand($this->runtime),
            new PolicyExportCommand($this->runtime),
            new PolicyListCommand($this->runtime),
            new PolicyMigrateCommand($this->runtime),
            new AdminPolicyImportCommand($this->runtime),
            new AdminPolicyActivateCommand($this->runtime),
            new AdminPolicyExportCommand($this->runtime),
            new AdminRebacStatsCommand($this->runtime),
            new JanitorGcCommand($this->runtime),
            new JanitorAuditGcCommand($this->runtime),
            new JanitorReplayGcCommand($this->runtime),
            new JanitorArchiveAuditCommand($this->runtime),
            new BatchPerfCommand(new BatchPerfRuntime(), new PerfStatsService(), new PerfStatsReport()),
            new BenchCommand(new BenchRuntime(), new BenchStatsService(), new BenchStatsReport()),
            new PerfStatsCommand(new BatchPerfRuntime(), new PerfStatsService(), new PerfStatsReport()),
            new BenchStatsCommand(new BenchRuntime(), new BenchStatsService(), new BenchStatsReport()),
            new PerfReportCommand(new BatchPerfRuntime(), new PerfStatsService(), new PerfStatsReport()),
            new BenchReportCommand(new BenchRuntime(), new BenchStatsService(), new BenchStatsReport()),
            new PerfRegressionCheckCommand(new BatchPerfRuntime(), new PerfStatsService(), new PerfStatsReport(), new PerfThresholdEvaluator(), new JsonReportLoader(), new PerfRegressionComparator()),
            new BenchRegressionCheckCommand(new BenchRuntime(), new BenchStatsService(), new BenchStatsReport(), new BenchThresholdEvaluator(), new JsonReportLoader(), new BenchRegressionComparator()),
            new ProfileCatalogListCommand(new ComparisonProfileCatalog()),
            new ProfileAutoPromoteCommand(new BaselineManifestManager(), new JsonReportLoader()),
            new ProfileReportCommand(new BaselineManifestManager(), new ComparisonProfileCatalog(), new JsonReportLoader()),
            new PerfBaselinePromoteCommand(new BaselineManifestManager(), new JsonReportLoader()),
            new BenchBaselinePromoteCommand(new BaselineManifestManager(), new JsonReportLoader()),
            new PerfProfileCheckCommand(new BatchPerfRuntime(), new PerfStatsService(), new PerfStatsReport(), new PerfThresholdEvaluator(), new ComparisonProfileCatalog(), new BaselineManifestManager(), new JsonReportLoader(), new PerfRegressionComparator()),
            new BenchProfileCheckCommand(new BenchRuntime(), new BenchStatsService(), new BenchStatsReport(), new BenchThresholdEvaluator(), new ComparisonProfileCatalog(), new BaselineManifestManager(), new JsonReportLoader(), new BenchRegressionComparator()),
        ];
    }
}
