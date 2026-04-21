<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Acl\Source;

use App\Rolling\Entity\Role\SubjectId;

interface GithubSubjectResolver
{
    /**
     * @param SubjectId $subject
     *
     * @return string|null
     */
    public function githubLogin(SubjectId $subject): ?string;
}

final class DefaultGithubResolver implements GithubSubjectResolver
{
    /**
     * @param SubjectId $subject
     *
     * @return string|null
     */
    public function githubLogin(SubjectId $subject): ?string
    {
        $s = $subject->value();
        if (str_starts_with($s, 'github:')) {
            return substr($s, 7);
        }

        return $s; // fallback — прямое соответствие
    }
}
