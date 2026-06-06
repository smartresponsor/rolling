<?php

declare(strict_types=1);

namespace App\Rolling\InfrastructureInterface\Acl\Source;

use App\Rolling\Entity\Role\SubjectId;

interface GithubSubjectResolverInterface
{
    public function githubLogin(SubjectId $subject): ?string;
}
