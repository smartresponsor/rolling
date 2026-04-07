<?php
declare(strict_types=1);

namespace App\Infrastructure\Symfony\DependencyInjection;

use App\Infrastructure\Audit\PdoAuditWriter;
use App\Infrastructure\Cache\InMemoryCache;
use App\Controller\V2\AccessController;
use App\Infrastructure\Symfony\EventSubscriber\HmacGuardSubscriber;
use App\Legacy\Invalidation\SubjectEpochs;
use App\Net\Http\PhpStreamHttpClient;
use App\Infrastructure\Observability\Metrics\Decorators\MetricsPdpV2;
use App\Infrastructure\Observability\Metrics\PrometheusExporter;
use App\Infrastructure\Observability\Metrics\Registry;
use App\Security\Http\HmacRequestVerifier;
use App\Infrastructure\Security\Replay\PdoReplayNonceStore;
use PDO;
use App\Legacy\Policy\Client\V2\RemotePdpV2;
use App\Legacy\Policy\Decorator\V2\AuditingPdp;
use App\Legacy\Policy\Decorator\V2\CachedPdpV2;
use App\Legacy\Policy\Decorator\V2\RegistryBackedPdp;
use App\Legacy\Policy\Registry\InMemorySource;
use App\Legacy\Policy\Registry\PolicyRegistry;
use App\PolicyInterface\PdpV2Interface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;

/**
 *
 */

/**
 *
 */
final class RoleExtension extends Extension
{
    /**
     * @param array $configs
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @return void
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        // Basic HTTP client (default: PhpStreamHttpClient)
        $container->register('role.http', PhpStreamHttpClient::class);

        // Base PDP (remote or inproc placeholder)
        if ($config['pdp']['mode'] === 'remote') {
            $def = $container->register('role.pdp.base', RemotePdpV2::class);
            $def->setArguments([
                $config['pdp']['remote']['base_url'],
                new Reference('role.http'),
                $config['pdp']['remote']['api_key'],
                $config['pdp']['remote']['hmac_secret'],
                $config['pdp']['remote']['timeout_ms'],
                $config['pdp']['remote']['retries'],
                null, // CircuitBreaker опционален — можно добавить позже
            ]);
        } else {
            // In-proc PDP — ожидается, что реализован где-то в проекте, подставим алиас на base
            $container->register('role.pdp.base', PdpV2Interface::class);
        }

        $pdpService = 'role.pdp.base';

        // Registry decorator
        if ($config['pdp']['registry']['enabled']) {
            $container->register('role.registry.source', InMemorySource::class)
                ->setArguments([$this->loadJson($config['pdp']['registry']['path'])]);
            $container->register('role.registry', PolicyRegistry::class)
                ->setArguments([new Reference('role.registry.source')]);

            $container->register('role.pdp.registry', RegistryBackedPdp::class)
                ->setArguments([new Reference($pdpService), new Reference('role.registry')]);
            $pdpService = 'role.pdp.registry';
        }

        // Cache decorator
        if ($config['pdp']['cache']['enabled']) {
            $container->register('role.cache', InMemoryCache::class);
            $container->register('role.epochs', SubjectEpochs::class);
            $container->register('role.pdp.cached', CachedPdpV2::class)
                ->setArguments([new Reference($pdpService), new Reference('role.cache'), new Reference('role.epochs'), $config['pdp']['cache']['ttl_seconds']]);
            $pdpService = 'role.pdp.cached';
        }

        // Audit decorator
        if ($config['pdp']['audit']['enabled'] && $config['pdp']['audit']['pdo_dsn']) {
            $pdoDef = $container->register('role.audit.pdo', PDO::class)->setArguments([
                $config['pdp']['audit']['pdo_dsn'],
                $config['pdp']['audit']['pdo_user'],
                $config['pdp']['audit']['pdo_pass'],
            ]);
            $container->register('role.audit.writer', PdoAuditWriter::class)->setArguments([new Reference('role.audit.pdo')]);
            $container->register('role.pdp.auditing', AuditingPdp::class)->setArguments([new Reference($pdpService), new Reference('role.audit.writer')]);
            $pdpService = 'role.pdp.auditing';
        }

        // Metrics decorator
        if ($config['pdp']['metrics']['enabled']) {
            $container->register('role.metrics.registry', Registry::class);
            $container->register('role.pdp.metrics', MetricsPdpV2::class)
                ->setArguments([new Reference($pdpService), new Reference('role.metrics.registry'), $config['pdp']['metrics']['component']]);
            $pdpService = 'role.pdp.metrics';
            // prometheus exporter (для /metrics контроллера, если понадобится)
            $container->register('role.metrics.exporter', PrometheusExporter::class)
                ->setArguments([new Reference('role.metrics.registry')]);
        }

        // Экспорт публичного сервиса PDP v2
        $container->setAlias('role.pdp.v2', $pdpService)->setPublic(true);

        // Controllers
        $container->register('role.controller.v2', AccessController::class)
            ->setArguments([new Reference('role.pdp.v2')])
            ->setPublic(true);

        // HMAC + anti-replay guard (Request subscriber)
        if ($config['security']['hmac_enabled'] && $config['security']['hmac_secret']) {
            $container->register('role.hmac.verifier', HmacRequestVerifier::class)
                ->setArguments([$config['security']['hmac_secret'], $config['security']['allowed_skew_sec']]);
            if ($config['security']['anti_replay']['enabled'] && $config['security']['anti_replay']['pdo_dsn']) {
                $container->register('role.replay.pdo', PDO::class)->setArguments([
                    $config['security']['anti_replay']['pdo_dsn'],
                    $config['security']['anti_replay']['pdo_user'],
                    $config['security']['anti_replay']['pdo_pass'],
                ]);
                $container->register('role.replay.store', PdoReplayNonceStore::class)
                    ->setArguments([new Reference('role.replay.pdo')]);
            } else {
                $container->register('role.replay.store', PdoReplayNonceStore::class)
                    ->setArguments([new Reference('role.audit.pdo')]) // fallback если audit pdo есть
                    ->setPublic(false);
            }
            $container->register('role.hmac.subscriber', HmacGuardSubscriber::class)
                ->setArguments([new Reference('role.hmac.verifier'), new Reference('role.replay.store'), '/v2/access/check', $config['security']['anti_replay']['ttl_sec']])
                ->addTag('kernel.event_subscriber');
        }
    }

    /** @return array<string,mixed> */
    private function loadJson(string $path): array
    {
        if (!is_file($path)) return [];
        $data = json_decode((string)@file_get_contents($path), true);
        return is_array($data) ? $data : [];
    }
}
