<?php

declare(strict_types=1);

namespace App\Rolling\Command\Console;

use App\Rolling\Tests\Support\RoleFixtureCatalog;
use App\Rolling\Tests\Support\RoleScenarioRunner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Implements the AbstractRoleCommand console workflow.
 */
abstract class AbstractRoleCommand extends Command
{
    /**
     * Resolve a named role fixture for command execution.
     */
    protected function fixture(string $nameEntity): array
    {
        if ('' === $nameEntity) {
            throw new \InvalidArgumentException('Fixture nameEntity is required.');
        }

        return RoleFixtureCatalog::get($nameEntity);
    }

    /**
     * Validate and return a scenario name.
     */
    protected function scenario(string $nameEntity): string
    {
        if ('' === $nameEntity) {
            throw new \InvalidArgumentException('Scenario nameEntity is required.');
        }

        return $nameEntity;
    }

    /**
     * Write a JSON payload to command output and return success.
     */
    protected function writeJson(OutputInterface $output, array $payload): int
    {
        $output->writeln(json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION));

        return Command::SUCCESS;
    }

    /**
     * Write a throwable message and return an invalid-command status.
     */
    protected function writeThrowable(OutputInterface $output, \Throwable $throwable): int
    {
        $output->writeln($throwable->getMessage());

        return Command::INVALID;
    }

    /**
     * Run the baseline scenario for a fixture.
     */
    protected function baseline(array $fixture): array
    {
        return RoleScenarioRunner::runBaseline($fixture);
    }

    /**
     * Preview a named scenario for a fixture.
     */
    protected function preview(array $fixture, string $scenario): array
    {
        return RoleScenarioRunner::preview($fixture, $scenario);
    }

    /**
     * Run a named scenario for a fixture.
     */
    protected function runScenario(array $fixture, string $scenario): array
    {
        return RoleScenarioRunner::runScenario($fixture, $scenario);
    }

    /**
     * Build an explanation for a relationship decision.
     */
    protected function explain(array $fixture, string $subject, string $object, string $relation, ?string $scenario = null): array
    {
        return RoleScenarioRunner::explain($fixture, $subject, $object, $relation, $scenario);
    }

    /**
     * Run the audit scenario for a fixture.
     */
    protected function audit(array $fixture): array
    {
        return RoleScenarioRunner::audit($fixture);
    }

    /**
     * Return the available fixture names.
     */
    protected function fixtureNames(): array
    {
        return RoleFixtureCatalog::names();
    }

    /**
     * Return the available scenario names for a fixture.
     */
    protected function scenarioNames(array $fixture): array
    {
        return RoleScenarioRunner::scenarioNames($fixture);
    }
}
