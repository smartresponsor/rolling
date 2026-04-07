<?php
declare(strict_types=1);

namespace App\Legacy\Acl\Source;

interface GithubSubjectResolver extends \App\Infrastructure\Acl\Source\GithubSubjectResolver
{
}

class_alias(\App\Infrastructure\Acl\Source\DefaultGithubResolver::class, __NAMESPACE__ . '\DefaultGithubResolver');
