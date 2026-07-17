<?php

declare(strict_types=1);

/*
 * Migration tombstone.
 *
 * The duplicate RollingPermissionCrudController implementation was retired in
 * favour of RollingRolePermissionCrudController and rolling.role-permission
 * metadata. Repository policy currently prevents touched-file deletion, so this
 * class-free file remains only to preserve an explicit, auditable retirement.
 */
