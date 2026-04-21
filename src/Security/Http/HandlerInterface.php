<?php

declare(strict_types=1);

namespace App\Rolling\Security\Http;

interface HandlerInterface
{
    public function handle(RequestInterface $request): Response;
}
