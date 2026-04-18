<?php

declare(strict_types=1);

namespace App\Integration\Symfony\EventSubscriber;

use App\Infrastructure\Symfony\EventSubscriber\HmacGuardSubscriber as CanonicalHmacGuardSubscriber;

final class HmacGuardSubscriber extends CanonicalHmacGuardSubscriber
{
<<<<<<< HEAD
=======
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
        private readonly int            $nonceTtlSec = 600,
    ) {}

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
>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa
}
