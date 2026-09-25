<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Console\Support;

use App\Rolling\Command\Console\AdminPolicyActivateCommand;
use App\Rolling\Command\Console\AdminPolicyExportCommand;
use App\Rolling\Command\Console\AdminPolicyImportCommand;
use App\Rolling\Command\Console\AdminRebacStatsCommand;
use App\Rolling\Command\Console\AuditCommand;
use App\Rolling\Command\Console\BatchPerfCommand;
use App\Rolling\Command\Console\BenchBaselinePromoteCommand;
use App\Rolling\Command\Console\BenchCommand;
use App\Rolling\Command\Console\BenchProfileCheckCommand;
use App\Rolling\Command\Console\BenchRegressionCheckCommand;
use App\Rolling\Command\Console\BenchReportCommand;
use App\Rolling\Command\Console\BenchStatsCommand;
use App\Rolling\Command\Console\ExplainCommand;
use App\Rolling\Command\Console\FixtureListCommand;
use App\Rolling\Command\Console\FixtureShowCommand;
use App\Rolling\Command\Console\FixtureSmokeCommand;
use App\Rolling\Command\Console\JanitorArchiveAuditCommand;
use App\Rolling\Command\Console\JanitorAuditGcCommand;
use App\Rolling\Command\Console\JanitorGcCommand;
use App\Rolling\Command\Console\JanitorReplayGcCommand;
use App\Rolling\Command\Console\PerfBaselinePromoteCommand;
use App\Rolling\Command\Console\PerfProfileCheckCommand;
use App\Rolling\Command\Console\PerfRegressionCheckCommand;
use App\Rolling\Command\Console\PerfReportCommand;
use App\Rolling\Command\Console\PerfStatsCommand;
use App\Rolling\Command\Console\PolicyActivateCommand;
use App\Rolling\Command\Console\PolicyExportCommand;
use App\Rolling\Command\Console\PolicyImportCommand;
use App\Rolling\Command\Console\PolicyListCommand;
use App\Rolling\Command\Console\PolicyMigrateCommand;
use App\Rolling\Command\Console\ProfileAutoPromoteCommand;
use App\Rolling\Command\Console\ProfileCatalogListCommand;
use App\Rolling\Command\Console\ProfileReportCommand;
use App\Rolling\Command\Console\RebacCheckCommand;
use App\Rolling\Command\Console\RebacWriteCommand;
use App\Rolling\Command\Console\ScenarioListCommand;
use App\Rolling\Command\Console\ScenarioOperationCommand;
use App\Rolling\Command\Console\ScenarioRunCommand;
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
