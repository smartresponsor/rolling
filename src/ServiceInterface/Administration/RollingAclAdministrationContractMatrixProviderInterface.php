<?php

declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Administration;

use App\Rolling\Value\Administration\RollingAclAdministrationContractMatrix;

interface RollingAclAdministrationContractMatrixProviderInterface
{
    public function matrix(): RollingAclAdministrationContractMatrix;
}
