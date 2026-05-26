<?php

declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Administration;

use App\Rolling\Value\Administration\RollingAclMutationRequest;
use App\Rolling\Value\Administration\RollingAclMutationValidationResult;

/**
 * Validates ACL mutation requests before persistence or policy execution.
 */
interface RollingAclMutationValidatorInterface
{
    public function validate(RollingAclMutationRequest $request): RollingAclMutationValidationResult;

    /** @return list<string> */
    public function allowedMutationTypes(): array;
}
