<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Acl\Source;

use App\Rolling\Entity\Role\SubjectId;
use App\Rolling\InfrastructureInterface\Acl\Source\GithubSubjectResolverInterface;

final class DefaultGithubSubjectResolver implements GithubSubjectResolverInterface
{
    public function githubLogin(SubjectId $subject): ?string
    {
        $value = $subject->value();
        if (str_starts_with($value, 'github:')) {
            return substr($value, 7);
        }

        return $value;
    }
}
