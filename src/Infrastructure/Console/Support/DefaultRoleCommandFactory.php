<?php

declare(strict_types=1);

namespace App\Infrastructure\Console\Support;

use App\Infrastructure\Console\Command\AdminPolicyActivateCommand;
use App\Infrastructure\Console\Command\AdminPolicyExportCommand;
use App\Infrastructure\Console\Command\AdminPolicyImportCommand;
use App\Infrastructure\Console\Command\AdminRebacStatsCommand;
use App\Infrastructure\Console\Command\AuditCommand;
use App\Infrastructure\Console\Command\BatchPerfCommand;
use App\Infrastructure\Console\Command\BenchCommand;
use App\Infrastructure\Console\Command\BenchReportCommand;
use App\Infrastructure\Console\Command\BenchBaselinePromoteCommand;
use App\Infrastructure\Console\Command\BenchProfileCheckCommand;
use App\Infrastructure\Console\Command\BenchRegressionCheckCommand;
use App\Infrastructure\Console\Command\BenchStatsCommand;
use App\Infrastructure\Console\Command\ExplainCommand;
use App\Infrastructure\Console\Command\FixtureListCommand;
use App\Infrastructure\Console\Command\FixtureShowCommand;
use App\Infrastructure\Console\Command\FixtureSmokeCommand;
use App\Infrastructure\Console\Command\JanitorArchiveAuditCommand;
use App\Infrastructure\Console\Command\JanitorAuditGcCommand;
use App\Infrastructure\Console\Command\JanitorGcCommand;
use App\Infrastructure\Console\Command\JanitorReplayGcCommand;
use App\Infrastructure\Console\Command\PerfBaselinePromoteCommand;
use App\Infrastructure\Console\Command\PerfReportCommand;
use App\Infrastructure\Console\Command\PerfProfileCheckCommand;
use App\Infrastructure\Console\Command\PerfRegressionCheckCommand;
use App\Infrastructure\Console\Command\ProfileAutoPromoteCommand;
use App\Infrastructure\Console\Command\ProfileCatalogListCommand;
use App\Infrastructure\Console\Command\ProfileReportCommand;
use App\Infrastructure\Console\Command\PerfStatsCommand;
use App\Infrastructure\Console\Command\PolicyActivateCommand;
use App\Infrastructure\Console\Command\PolicyExportCommand;
use App\Infrastructure\Console\Command\PolicyImportCommand;
use App\Infrastructure\Console\Command\PolicyListCommand;
use App\Infrastructure\Console\Command\PolicyMigrateCommand;
use App\Infrastructure\Console\Command\RebacCheckCommand;
use App\Infrastructure\Console\Command\RebacWriteCommand;
use App\Infrastructure\Console\Command\ScenarioListCommand;
use App\Infrastructure\Console\Command\ScenarioOperationCommand;
use App\Infrastructure\Console\Command\ScenarioRunCommand;
use App\Infrastructure\Console\Contract\RoleCommandFactoryInterface;
use App\Infrastructure\Console\Support\BatchPerfRuntime;
use App\Infrastructure\Console\Support\BaselineManifestManager;
use App\Infrastructure\Console\Support\ComparisonProfileCatalog;
use App\Infrastructure\Console\Support\BenchRegressionComparator;
use App\Infrastructure\Console\Support\BenchRuntime;
use App\Infrastructure\Console\Support\BenchStatsReport;
use App\Infrastructure\Console\Support\BenchThresholdEvaluator;
use App\Infrastructure\Console\Support\BenchStatsService;
use App\Infrastructure\Console\Support\JsonReportLoader;
use App\Infrastructure\Console\Support\PerfRegressionComparator;
use App\Infrastructure\Console\Support\PerfStatsReport;
use App\Infrastructure\Console\Support\PerfThresholdEvaluator;
use App\Infrastructure\Console\Support\PerfStatsService;
use Symfony\Component\Console\Command\Command;

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
