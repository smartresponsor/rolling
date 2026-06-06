<?php

declare(strict_types=1);

namespace App\Rolling\ServiceInterface\View;

use App\Rolling\Value\View\RollSurfaceOutput;

/**
 * Exports Rolling UI data without binding Rolling to Interfacing.
 */
interface RollSurfaceProviderInterface
{
    public function summary(): RollSurfaceOutput;
}
