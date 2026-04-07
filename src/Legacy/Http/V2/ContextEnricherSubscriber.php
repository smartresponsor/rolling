<?php
declare(strict_types=1);

namespace App\Legacy\Http\V2;

use App\Service\Attribute\AttributeService;
use App\Legacy\Http\V2\Context\ContextMerge;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 *
 */

/**
 *
 */
final class ContextEnricherSubscriber implements EventSubscriberInterface
{
    /**
     * @param \App\Legacy\Attribute\AttributeService $attrs
     * @param array $cfg
     */
    public function __construct(private readonly AttributeService $attrs, private readonly array $cfg = [])
    {
    }

    /**
     * @return array[]
     */
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::CONTROLLER_ARGUMENTS => [['onArgs', 11]]];
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent $ev
     * @return void
     */
    public function onArgs(ControllerArgumentsEvent $ev): void
    {
        $req = $ev->getRequest();
        if (($req->headers->get('X-Role-No-Enrich') ?? '') === '1' || $req->query->getBoolean('no_enrich')) return;
        $payload = json_decode((string)$req->getContent(), true) ?: [];
        $ctx = (array)($payload['context'] ?? []);
        $user = (string)($ctx['user_id'] ?? '');
        $org = (string)($ctx['org_id'] ?? '');
        $res = (string)($ctx['resource_id'] ?? '');
        $server = [];
        if ($user !== '') $server['user'] = $this->attrs->user($user);
        if ($org !== '') $server['org'] = $this->attrs->org($org);
        if ($res !== '') $server['resource'] = $this->attrs->resource($res);
        $payload['_role_ctx_enriched'] = ContextMerge::merge($ctx, $server);
        $req->attributes->set('_role_payload', $payload);
    }
}
