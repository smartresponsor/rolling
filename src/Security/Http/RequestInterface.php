<?php

declare(strict_types=1);

namespace App\Rolling\Security\Http;

interface RequestInterface
{
    public function method(): string;

    public function path(): string;

    public function header(string $name): ?string;

    public function body(): string;
}
