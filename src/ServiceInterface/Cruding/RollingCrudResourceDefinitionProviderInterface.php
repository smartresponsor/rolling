<?php

declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Cruding;

use App\Rolling\Value\Cruding\RollingCrudResourceDefinition;

/**
 * Exposes Rolling resource metadata for the future Cruding adapter.
 */
interface RollingCrudResourceDefinitionProviderInterface
{
    /**
     * @return list<RollingCrudResourceDefinition>
     */
    public function definitions(): array;
}
