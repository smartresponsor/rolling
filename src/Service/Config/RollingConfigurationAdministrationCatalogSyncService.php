<?php

declare(strict_types=1);

namespace App\Rolling\Service\Config;

use App\Configuring\ServiceInterface\Config\ConfigToolServiceInterface;
use App\Configuring\ServiceInterface\Config\ConfigVariableToolServiceInterface;
use App\Configuring\ServiceInterface\Config\ManagedConfigVariablesProviderInterface;
use App\Configuring\Value\Config\ConfigToolDescriptor;
use App\Configuring\Value\Config\ConfigVariable;
use App\Configuring\Value\Config\ConfigVariableType;
use App\Rolling\ServiceInterface\Administration\RollingAdministrationCatalogSyncServiceInterface;

/**
 * Configuring-facing administration catalog synchronizer.
 *
 * The underlying catalog sync already belongs to Rolling. This tool simply
 * exposes it through Administering's generic Configuring form flow.
 */
final readonly class RollingConfigurationAdministrationCatalogSyncService implements ConfigToolServiceInterface, ManagedConfigVariablesProviderInterface, ConfigVariableToolServiceInterface
{
    public function __construct(private RollingAdministrationCatalogSyncServiceInterface $syncService)
    {
    }

    public function descriptor(): ConfigToolDescriptor
    {
        return new ConfigToolDescriptor(
            applicationCode: 'Rolling',
            toolCode: 'rolling.administration_catalog_sync',
            label: 'Rolling Administration Catalog Sync',
            description: 'Synchronize Rolling permissions, default roles, role hierarchy, and optional bootstrap assignments.',
            formClass: '',
            serviceClass: self::class,
            requiredPermission: 'administration.rolling.acl_mutation.apply',
            editableFields: [],
            sensitiveFields: [],
            readableFiles: [],
            writableFiles: [],
            metadata: [
                'section' => 'Rolling ACL',
                'kind' => 'administration_catalog_sync',
                'mutation_owner' => 'rolling',
            ],
            secretNames: [],
            applyStrategy: 'rolling_catalog_sync',
        );
    }

    /** @return iterable<ConfigVariable> */
    public function managedVariables(): iterable
    {
        yield ConfigVariable::yaml('catalog_sync.bootstrap_subject')
            ->withLabel('Bootstrap subject identifier')
            ->required(false);
        yield ConfigVariable::yaml('catalog_sync.bootstrap_user_identifier')
            ->withLabel('Bootstrap Symfony user identifier')
            ->required(false);
        yield ConfigVariable::yaml('catalog_sync.bootstrap_accessing_account_id')
            ->withLabel('Bootstrap Accessing account id')
            ->required(false);
        yield ConfigVariable::yaml('catalog_sync.apply_confirmed', null, ConfigVariableType::BOOL)
            ->withLabel('I reviewed this catalog synchronization')
            ->required(false);
    }

    public function loadVariableData(): array
    {
        return [
            'catalog_sync.bootstrap_subject' => '',
            'catalog_sync.bootstrap_user_identifier' => '',
            'catalog_sync.bootstrap_accessing_account_id' => '',
            'catalog_sync.apply_confirmed' => false,
        ];
    }

    public function saveVariables(array $variables, array $context = []): array
    {
        return [
            'status' => 'review_ready',
            'messages' => ['Rolling administration catalog sync is ready for review. Use Apply after confirming bootstrap subjects.'],
            'masked_changes' => $this->summary($variables),
            'file_changes' => [],
            'secret_changes' => [],
        ];
    }

    public function applyVariables(array $variables, array $context = []): array
    {
        if (true !== (bool) ($variables['catalog_sync.apply_confirmed'] ?? false)) {
            return [
                'status' => 'review_required',
                'messages' => ['Confirm review before synchronizing the Rolling administration catalog.'],
                'masked_changes' => $this->summary($variables),
                'file_changes' => [],
                'secret_changes' => [],
            ];
        }

        $bootstrapSubject = trim((string) ($variables['catalog_sync.bootstrap_subject'] ?? ''));
        $subjects = [];
        $userIdentifier = trim((string) ($variables['catalog_sync.bootstrap_user_identifier'] ?? ''));
        if ('' !== $userIdentifier) {
            $subjects[] = 'symfony:user:'.$userIdentifier;
        }

        $accessingAccountId = trim((string) ($variables['catalog_sync.bootstrap_accessing_account_id'] ?? ''));
        if ('' !== $accessingAccountId) {
            $subjects[] = 'accessing:account:'.$accessingAccountId;
        }

        $result = $this->syncService->sync('' !== $bootstrapSubject ? $bootstrapSubject : null, $subjects);

        return [
            'status' => 'applied',
            'messages' => ['Rolling administration catalog synchronized.'],
            'masked_changes' => $result->summary(),
            'file_changes' => [],
            'secret_changes' => [],
        ];
    }

    public function loadData(): object
    {
        return new \ArrayObject($this->loadVariableData());
    }

    public function save(object $data, array $context = []): array
    {
        return $this->saveVariables($this->objectToArray($data), $context);
    }

    public function apply(object $data, array $context = []): array
    {
        return $this->applyVariables($this->objectToArray($data), $context);
    }

    /** @return array<string, mixed> */
    private function summary(array $variables): array
    {
        return [
            'bootstrap_subject' => (string) ($variables['catalog_sync.bootstrap_subject'] ?? ''),
            'bootstrap_user_identifier' => (string) ($variables['catalog_sync.bootstrap_user_identifier'] ?? ''),
            'bootstrap_accessing_account_id' => (string) ($variables['catalog_sync.bootstrap_accessing_account_id'] ?? ''),
        ];
    }

    /** @return array<string, mixed> */
    private function objectToArray(object $data): array
    {
        return $data instanceof \Traversable ? iterator_to_array($data) : get_object_vars($data);
    }
}
