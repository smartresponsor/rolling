<?php
declare(strict_types=1);

namespace App\Legacy\Http\Security;

use App\Security\Role\Hmac\Verifier;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 *
 */

/**
 *
 */
final class HmacGuardSubscriber implements EventSubscriberInterface
{
    /**
     * @param \App\Security\Role\Hmac\Verifier $verifier
     * @param array $routes
     */
    public function __construct(private readonly Verifier $verifier, private readonly array $routes = ['role_rebac_write', 'role_policy_import', 'role_policy_activate'])
    {
    }

    /**
     * @return array[]
     */
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => ['onRequest', 40]];
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
     * @return void
     */
    public function onRequest(RequestEvent $event): void
    {
        $r = $event->getRequest();
        $route = (string)$r->attributes->get('_route');
        if (!in_array($route, $this->routes, true)) return;
        $hdrs = [];
        foreach ($r->headers->all() as $k => $v) {
            $hdrs[strtolower($k)] = is_array($v) ? ($v[0] ?? '') : (string)$v;
        }
        $path = $r->getPathInfo();
        $q = $r->getQueryString();
        if ($q) $path .= '?' . $q;
        $ok = $this->verifier->verify($r->getMethod(), $path, (string)$r->getContent(), $hdrs);
        if (!$ok) $event->setResponse(new JsonResponse(['ok' => false, 'error' => 'hmac_auth_failed'], 401));
    }
}
