<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\EventSubscriber;

use App\InfrastructureInterface\Security\ReplayNonceStoreInterface;
use App\Security\Http\HmacRequestVerifier;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class HmacGuardSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly HmacRequestVerifier $verifier,
        private readonly ReplayNonceStoreInterface $replayStore,
        private readonly string $path = '/v2/access/check',
        private readonly int $nonceTtlSec = 600,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => ['onKernelRequest', 102]];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if ($request->getPathInfo() !== $this->path || $request->getMethod() !== 'POST') {
            return;
        }

        $date = $request->headers->get('Date', '');
        $signature = $request->headers->get('X-Signature', '');
        $body = $request->getContent() ?: '';

        $result = $this->verifier->verify('POST', $this->path, $date, $body, $signature);
        if (!($result['ok'] ?? false)) {
            $reason = $result['reason'] ?? 'unauthorized';
            $event->setResponse(new JsonResponse(['error' => 'unauthorized', 'reason' => $reason], 401, ['X-Auth-Error' => $reason]));

            return;
        }

        $nonce = $request->headers->get('X-Nonce') ?: HmacRequestVerifier::derivedNonce($date, $body);
        if (!$this->replayStore->seen($nonce, $this->nonceTtlSec)) {
            $event->setResponse(new JsonResponse(['error' => 'unauthorized', 'reason' => 'replay'], 401, ['X-Auth-Error' => 'replay']));
        }
    }
}
