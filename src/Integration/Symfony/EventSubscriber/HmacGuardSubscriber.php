<?php
declare(strict_types=1);

namespace App\Integration\Symfony\EventSubscriber;

use Http\Security\HmacVerifier;
use Http\Security\Replay\StoreInterface;
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
     * @param \Http\Security\HmacVerifier $verifier
     * @param \Http\Security\Replay\StoreInterface $replayStore
     * @param string $path
     * @param int $nonceTtlSec
     */
    public function __construct(
        private readonly HmacVerifier   $verifier,
        private readonly StoreInterface $replayStore,
        private readonly string         $path = '/v2/access/check',
        private readonly int            $nonceTtlSec = 600
    )
    {
    }

    /**
     * @return array[]
     */
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => ['onKernelRequest', 102]]; // до контроллера
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
     * @return void
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $req = $event->getRequest();
        if ($req->getPathInfo() !== $this->path || $req->getMethod() !== 'POST') {
            return;
        }

        $date = $req->headers->get('Date', '');
        $sig = $req->headers->get('X-Signature', '');
        $body = $req->getContent() ?: '';

        $res = $this->verifier->verify('POST', $this->path, $date, $body, $sig);
        if (!($res['ok'] ?? false)) {
            $event->setResponse(new JsonResponse(['error' => 'unauthorized', 'reason' => $res['reason'] ?? ''], 401, ['X-Auth-Error' => $res['reason'] ?? 'unauthorized']));
            return;
        }

        $nonce = $req->headers->get('X-Nonce') ?: HmacVerifier::derivedNonce($date, $body);
        if (!$this->replayStore->seen($nonce, $this->nonceTtlSec)) {
            $event->setResponse(new JsonResponse(['error' => 'unauthorized', 'reason' => 'replay'], 401, ['X-Auth-Error' => 'replay']));
        }
    }
}
