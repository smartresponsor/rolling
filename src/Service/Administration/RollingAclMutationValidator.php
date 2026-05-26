<?php

declare(strict_types=1);

namespace App\Rolling\Service\Administration;

use App\Rolling\ServiceInterface\Administration\RollingAclMutationValidatorInterface;
use App\Rolling\Value\Administration\RollingAclMutationRequest;
use App\Rolling\Value\Administration\RollingAclMutationValidationResult;

/**
 * Strict metadata validator for Administering-driven ACL mutations.
 */
final class RollingAclMutationValidator implements RollingAclMutationValidatorInterface
{
    private const ALLOWED_MUTATIONS = [
        'role.assign',
        'role.revoke',
        'permission.grant',
        'permission.revoke',
        'acl.allow',
        'acl.deny',
    ];

    /** @return list<string> */
    public function allowedMutationTypes(): array
    {
        return self::ALLOWED_MUTATIONS;
    }

    public function validate(RollingAclMutationRequest $request): RollingAclMutationValidationResult
    {
        $violations = [];

        if (!in_array($request->mutationType(), self::ALLOWED_MUTATIONS, true)) {
            $violations[] = 'Unsupported Rolling ACL mutation type.';
        }

        if (!$this->safeKey($request->subjectIdentifier(), 240)) {
            $violations[] = 'Subject identifier is empty or contains forbidden characters.';
        }

        if (!$this->safeKey($request->permissionOrRoleKey(), 180)) {
            $violations[] = 'Permission or role key is empty or contains forbidden characters.';
        }

        if (!$this->safeKey($request->scopeKey(), 180)) {
            $violations[] = 'Scope key is empty or contains forbidden characters.';
        }

        if (!$this->safeKey($request->requestedBySubject(), 240)) {
            $violations[] = 'Requester subject is empty or contains forbidden characters.';
        }

        return [] === $violations ? RollingAclMutationValidationResult::ok() : RollingAclMutationValidationResult::invalid($violations);
    }

    private function safeKey(string $value, int $maxLength): bool
    {
        if ('' === trim($value) || mb_strlen($value) > $maxLength) {
            return false;
        }

        return 1 === preg_match('/^[a-zA-Z0-9_.:@\\/-]+$/', $value);
    }
}
