<?php

declare(strict_types=1);

namespace App\Rolling\Service\Context;

final class EnvContext
{
    /** @return array<string,mixed> */
    public function capture(): array
    {
        $subject = getenv('ROLE_SUBJECT') ?: '';

        return '' !== $subject ? ['subject' => $subject] : [];
    }
}
