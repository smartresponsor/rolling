<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Console\Command;

use App\Rolling\Tests\Support\RoleFixtureCatalog;
use App\Rolling\Tests\Support\RoleScenarioRunner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractRoleCommand extends Command
{
    protected function fixture(string $name): array
    {
        if ('' === $name) {
            throw new \InvalidArgumentException('Fixture name is required.');
        }

        return RoleFixtureCatalog::get($name);
    }

    protected function scenario(string $name): string
    {
        if ('' === $name) {
            throw new \InvalidArgumentException('Scenario name is required.');
        }

        return $name;
    }

    protected function writeJson(OutputInterface $output, array $payload): int
    {
        $output->writeln(json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION));

        return Command::SUCCESS;
    }

    protected function writeThrowable(OutputInterface $output, \Throwable $throwable): int
    {
        $output->writeln($throwable->getMessage());

        return Command::INVALID;
    }

    protected function baseline(array $fixture): array
    {
        return RoleScenarioRunner::runBaseline($fixture);
    }

    protected function preview(array $fixture, string $scenario): array
    {
        return RoleScenarioRunner::preview($fixture, $scenario);
    }

    protected function runScenario(array $fixture, string $scenario): array
    {
        return RoleScenarioRunner::runScenario($fixture, $scenario);
    }

    protected function explain(array $fixture, string $subject, string $object, string $relation, ?string $scenario = null): array
    {
        return RoleScenarioRunner::explain($fixture, $subject, $object, $relation, $scenario);
    }

    protected function audit(array $fixture): array
    {
        return RoleScenarioRunner::audit($fixture);
    }

    protected function fixtureNames(): array
    {
        return RoleFixtureCatalog::names();
    }

    protected function scenarioNames(array $fixture): array
    {
        return RoleScenarioRunner::scenarioNames($fixture);
    }
}
